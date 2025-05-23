<?php

namespace App\Http\Controllers;

use App\Services\JiraService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use GuzzleHttp\Client;



class DashboardController extends Controller
{
    protected string $baseUrl = '';
    protected string $token = '';
    public function __construct()
    {
        $this->baseUrl = env('JIRA_BASE_URL');
        $this->token = env('JIRA_API_TOKEN');
    }
    public function index(JiraService $jira)
    {
        $tasks = $jira->getUserAssignedTasks();
        return view('dashboard', compact('tasks'));
    }
    public function edit($id)
    {
        $user = Auth::user();
        $email = $user->jira_email;

        $client = new Client([
            'base_uri' => $this->baseUrl,
            'auth' => [$email, $this->token],
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);

        try {
            // Step 1: Get Task Details
            $response = $client->request('GET', "/rest/api/3/issue/{$id}");
            $task = json_decode($response->getBody(), true);

            // Step 2: Get Status Transitions
            $transitionResponse = $client->request('GET', "/rest/api/3/issue/{$id}/transitions");
            $transitions = json_decode($transitionResponse->getBody(), true);

            // Format transitions as a list of statuses
            $statuses = collect($transitions['transitions'])->map(function ($transition) {
                return [
                    'id' => $transition['id'],
                    'name' => $transition['to']['name'],
                ];
            })->all();

            return view('Tasks.edit', compact('task', 'statuses'));
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return redirect()->back()->with('error', 'Jira error: ' . $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Unexpected error: ' . $e->getMessage());
        }
    }
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $email = $user->jira_email;
        $token = $this->token;

        $request->validate([
            'summary' => 'required|string',
            'description' => 'required|string',
            'comment' => 'nullable|string',
            'status' => 'nullable|string', // transition ID
        ]);

        $client = new Client([
            'base_uri' => $this->baseUrl,
            'auth' => [$email, $token],
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);

        try {
            // Update summary and description
            $updateFields = [
                'fields' => [
                    'summary' => $request->summary,
                    'description' => [
                        'type' => 'doc',
                        'version' => 1,
                        'content' => [[
                            'type' => 'paragraph',
                            'content' => [[
                                'type' => 'text',
                                'text' => $request->description,
                            ]],
                        ]],
                    ],
                ]
            ];

            $client->request('PUT', "/rest/api/3/issue/{$id}", [
                'json' => $updateFields,
            ]);

            // Update status using transitions API
            if ($request->filled('status')) {
                $transitionData = [
                    'transition' => [
                        'id' => $request->status,
                    ],
                ];

                $client->request('POST', "/rest/api/3/issue/{$id}/transitions", [
                    'json' => $transitionData,
                ]);
            }

            // Add comment if provided
            if ($request->filled('comment')) {
                $commentBody = [
                    'body' => [
                        'type' => 'doc',
                        'version' => 1,
                        'content' => [[
                            'type' => 'paragraph',
                            'content' => [[
                                'type' => 'text',
                                'text' => $request->comment,
                            ]],
                        ]],
                    ]
                ];

                $client->request('POST', "/rest/api/3/issue/{$id}/comment", [
                    'json' => $commentBody,
                ]);
            }

            return redirect()->route('dashboard')->with('success', 'Issue updated successfully.');
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return redirect()->back()->with('error', 'Jira update failed: ' . $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Unexpected error: ' . $e->getMessage());
        }
    }
}

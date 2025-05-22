<?php

namespace App\Http\Controllers;

use App\Services\JiraService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
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
            $response = $client->request('GET', "/rest/api/3/issue/{$id}");
            $task = json_decode($response->getBody(), true);
            return view('Tasks.edit', compact('task'));
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

        // Validate input (optional, adjust rules as needed)
        $request->validate([
            'summary' => 'required|string',
            'description' => 'required|string',
            'comment' => 'nullable|string',
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

            return response()->json(['success' => true, 'message' => 'Issue updated successfully.']);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return response()->json(['error' => 'Jira update failed', 'details' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unexpected error', 'details' => $e->getMessage()], 500);
        }
    }
}

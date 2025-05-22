<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;

class JiraService
{
    protected string $baseUrl = '';
    protected string $token = '';

    public function __construct()
    {
        $this->baseUrl = env('JIRA_BASE_URL');
        $this->token = env('JIRA_API_TOKEN');
    }


    public function getUserAssignedTasks()
    {
        $user = Auth::user();

        $client = new Client([
            'base_uri' => $this->baseUrl,
            'auth' => [$user->email, $this->token],
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);

        try {
            $response = $client->request('GET', '/rest/api/3/search', [
                'query' => [
                    'jql' => 'assignee=currentUser()',
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            $tasks = collect($data['issues'])->sortBy([
                ['fields.priority.name', 'desc'],   // High > Low
                ['fields.project.name', 'asc'],     // A-Z
            ])->values()->all();

            return $tasks;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return response()->json([
                'error' => 'Jira client error',
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Unexpected error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use GuzzleHttp\Client;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    protected string $baseUrl = '';
    protected string $token = '';
    public function __construct()
    {
        $this->baseUrl = env('JIRA_BASE_URL');
        $this->token = env('JIRA_API_TOKEN');
    }
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Authenticate Laravel user
        $request->authenticate();
        $request->session()->regenerate();
        $user = Auth::user();
        $email = $user->jira_email;
        // Guzzle client for Jira
        $client = new Client([
            'base_uri' => $this->baseUrl,
            'auth' => [$email, $this->token],
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);

        try {
            // Attempt to authenticate with Jira
            $response = $client->request('GET', '/rest/api/3/myself');
            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Jira authentication failed.');
            }
            return redirect()->intended('/dashboard');
        } catch (\Exception $e) {
            Auth::logout();
            return back()->withErrors([
                'jira' => 'Failed to authenticate with Jira. Please check your Jira credentials.',
            ]);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}

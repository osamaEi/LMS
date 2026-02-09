<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;

class FutureXSsoService
{
    private string $clientId;
    private string $clientSecret;
    private string $redirectUri;
    private string $authorizeUrl;
    private string $tokenUrl;

    public function __construct()
    {
        $this->clientId = config('services.futurex.client_id', '');
        $this->clientSecret = config('services.futurex.client_secret', '');
        $this->redirectUri = config('services.futurex.redirect', '');
        $this->authorizeUrl = config('services.futurex.authorize_url', '');
        $this->tokenUrl = config('services.futurex.token_url', '');
    }

    public function getAuthorizationUrl(string $state): string
    {
        $params = http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'scope' => 'openid profile email',
            'state' => $state,
        ]);

        return $this->authorizeUrl . '?' . $params;
    }

    public function handleCallback(string $code): array
    {
        $response = Http::asForm()->post($this->tokenUrl, [
            'grant_type' => 'authorization_code',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'redirect_uri' => $this->redirectUri,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to obtain access token from FutureX');
        }

        $tokenData = $response->json();

        // Get user profile
        $userResponse = Http::withToken($tokenData['access_token'])
            ->get(config('services.futurex.userinfo_url', ''));

        if (!$userResponse->successful()) {
            throw new \Exception('Failed to obtain user info from FutureX');
        }

        return $userResponse->json();
    }

    public function findOrCreateUser(array $ssoProfile): User
    {
        $futurexId = $ssoProfile['sub'] ?? $ssoProfile['id'];

        $user = User::where('futurex_id', $futurexId)->first();

        if (!$user) {
            $user = User::create([
                'name' => $ssoProfile['name'] ?? 'FutureX User',
                'email' => $ssoProfile['email'],
                'futurex_id' => $futurexId,
                'sso_provider' => 'futurex',
                'sso_metadata' => $ssoProfile,
                'sso_linked_at' => now(),
                'password' => bcrypt(str()->random(32)),
                'role' => 'student', // Default role
            ]);
        }

        return $user;
    }

    public function linkAccount(User $user, array $ssoProfile): void
    {
        $user->update([
            'futurex_id' => $ssoProfile['sub'] ?? $ssoProfile['id'],
            'sso_provider' => 'futurex',
            'sso_metadata' => $ssoProfile,
            'sso_linked_at' => now(),
        ]);
    }
}

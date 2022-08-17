<?php

declare(strict_types=1);

namespace EDB\AdminBundle\Security;

use EDB\AdminBundle\Util\StringUtils;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GoogleHelper
{
    const GOOGLE_API_BASE = 'https://www.googleapis.com';
    const GOOGLE_ACCOUNT_BASE = 'https://accounts.google.com';

    private string $googleSecret;
    private string $googleId;
    private RouterInterface $router;
    private SessionInterface $session;
    private HttpClientInterface $client;

    public function __construct(string $googleSecret, string $googleId, RouterInterface $router, SessionInterface $session, HttpClientInterface $client)
    {
        $this->googleSecret = $googleSecret;
        $this->googleId = $googleId;
        $this->router = $router;
        $this->session = $session;
        $this->client = $client;
    }

    public function getLoginUrl()
    {
        $authorizeURL = self::GOOGLE_ACCOUNT_BASE . '/o/oauth2/v2/auth?';

        $this->session->set('state', StringUtils::generateRandomString());

        $params = array(
            'response_type' => 'code',
            'client_id' => $this->googleId,
            'redirect_uri' => $this->getAppBaseUrl(),
            'scope' => 'openid email',
            'state' => $this->session->get('state')
        );

        return $authorizeURL . http_build_query($params);
    }

    public function getUserIdentifier(string $code): ?string
    {
        $tokenURL = self::GOOGLE_API_BASE . '/oauth2/v4/token';

        $response = $this->client->request('POST', $tokenURL, [
            'body' => http_build_query([
                'grant_type' => 'authorization_code',
                'client_id' => $this->googleId,
                'client_secret' => $this->googleSecret,
                'redirect_uri' => $this->getAppBaseUrl(),
                'code' => $code
            ])
        ]);

        $responseData = json_decode($response->getContent(), true);
        $token = $responseData['access_token'];

        $response = $this->client->request('GET', self::GOOGLE_API_BASE . '/oauth2/v3/userinfo', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token
            ]
        ]);

        $responseData = json_decode($response->getContent(), true);

        return $responseData['email'];
    }

    private function getAppBaseUrl(): string
    {
        return $this->router->generate('dashboard', [], UrlGeneratorInterface::ABSOLUTE_URL);
    }
}

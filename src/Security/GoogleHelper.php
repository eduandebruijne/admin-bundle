<?php

declare(strict_types=1);

namespace EDB\AdminBundle\Security;

use EDB\AdminBundle\Util\StringUtils;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GoogleHelper
{
    const GOOGLE_API_BASE = 'https://www.googleapis.com';
    const GOOGLE_ACCOUNT_BASE = 'https://accounts.google.com';

    protected string $googleSecret;
    protected string $googleId;
    protected RouterInterface $router;
    protected RequestStack $requestStack;
    protected HttpClientInterface $client;

    public function __construct(string $googleSecret, string $googleId, RouterInterface $router, RequestStack $requestStack, HttpClientInterface $client)
    {
        $this->googleSecret = $googleSecret;
        $this->googleId = $googleId;
        $this->router = $router;
        $this->requestStack = $requestStack;
        $this->client = $client;
    }

    public function getLoginUrl()
    {
        $authorizeURL = self::GOOGLE_ACCOUNT_BASE . '/o/oauth2/v2/auth?';

        $this->requestStack->getSession()->set('state', StringUtils::generateRandomString());

        $params = array(
            'response_type' => 'code',
            'client_id' => $this->googleId,
            'redirect_uri' => $this->getAppBaseUrl(),
            'scope' => 'openid email',
            'state' => $this->requestStack->getSession()->get('state')
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

    protected function getAppBaseUrl(): string
    {
        return $this->router->generate('dashboard', [], UrlGeneratorInterface::ABSOLUTE_URL);
    }
}

<?php

namespace EDB\AdminBundle\Security;

use DateTime;
use Firebase\JWT\JWT;

class TokenManager
{
    private string $appSecret;

    public function __construct(string $appSecret)
    {
        $this->appSecret = $appSecret;
    }

    /**
     * @param string $ttl Example '1 week'
     */
    public function generate(?string $ttl = null): string
    {
        $expiresAt = [];
        if (!empty($ttl)) {
            $expiresAt = [
                'exp' => (new DateTime('now'))
                    ->modify($ttl)
                    ->getTimestamp()
            ];
        }

        return JWT::encode(array_merge($expiresAt, [
            'iat' => (new DateTime('now'))->getTimestamp()
        ]), $this->appSecret);
    }

    public function validate(string $token): object
    {
        return JWT::decode($token, $this->appSecret, ['HS256']);
    }
}
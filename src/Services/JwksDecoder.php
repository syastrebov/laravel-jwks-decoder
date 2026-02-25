<?php

declare(strict_types=1);

namespace JwksDecoder\Services;

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use JwksDecoder\Contracts\JwksDecoderInterface;

final readonly class JwksDecoder implements JwksDecoderInterface
{
    #[\Override]
    public function decodeToken(string $token, array $jwks): array
    {
        $keys = JWK::parseKeySet($jwks);

        return (array) JWT::decode($token, reset($keys));
    }
}

<?php

declare(strict_types=1);

namespace JwksDecoder\Contracts;

interface JwksDecoderInterface
{
    /**
     * @return array<string, mixed>
     */
    public function decodeToken(string $token, array $jwks): array;
}

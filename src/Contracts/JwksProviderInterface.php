<?php

declare(strict_types=1);

namespace JwksDecoder\Contracts;

interface JwksProviderInterface
{
    /** @return array<string, mixed> */
    public function getJwks(): array;

    public function invalidate(): void;
}

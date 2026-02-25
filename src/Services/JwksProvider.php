<?php

declare(strict_types=1);

namespace JwksDecoder\Services;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Http\Client\Factory as HttpFactory;
use JwksDecoder\Contracts\JwksProviderInterface;

final readonly class JwksProvider implements JwksProviderInterface
{
    public function __construct(
        private CacheRepository $cache,
        private HttpFactory $http,
        private string $url,
        private int $ttl,
        private string $cacheKey
    ) {
    }

    #[\Override]
    public function getJwks(): array
    {
        return $this->cache->remember($this->cacheKey, $this->ttl, function () {
            return $this->http->get($this->url)->throw()->json();
        });
    }

    #[\Override]
    public function invalidate(): void
    {
        $this->cache->forget($this->cacheKey);
    }
}

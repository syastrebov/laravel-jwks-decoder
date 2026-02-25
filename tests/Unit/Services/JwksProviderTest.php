<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use JwksDecoder\Services\JwksProvider;
use Tests\TestCase;

final class JwksProviderTest extends TestCase
{
    private string $url = 'https://auth.example.com/jwks.json';

    private string $cacheKey = 'test_jwks_key';

    private int $ttl = 3600;

    private JwksProvider $provider;

    public function test_it_fetches_and_caches_jwks(): void
    {
        $mockData = ['keys' => [['kid' => 'test-id', 'alg' => 'RS256']]];

        Http::fake([
            $this->url => Http::response($mockData, 200),
        ]);

        $result = $this->provider->getJwks();

        $this->assertEquals($mockData, $result);
        $this->assertTrue(Cache::has($this->cacheKey));
        $this->assertEquals($mockData, Cache::get($this->cacheKey));

        $this->provider->getJwks();
        Http::assertSentCount(1);
    }

    public function test_it_invalidates_cache(): void
    {
        Cache::put($this->cacheKey, ['old-data'], $this->ttl);
        $this->assertTrue(Cache::has($this->cacheKey));

        $this->provider->invalidate();

        $this->assertFalse(Cache::has($this->cacheKey));
    }

    public function test_it_throws_exception_on_http_error(): void
    {
        Http::fake([
            $this->url => Http::response([], 500),
        ]);

        $this->expectException(\Illuminate\Http\Client\RequestException::class);

        $this->provider->getJwks();
    }

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        Cache::forget($this->cacheKey);

        $this->provider = new JwksProvider(
            cache: Cache::driver(),
            http: $this->app->make(\Illuminate\Http\Client\Factory::class),
            url: $this->url,
            ttl: $this->ttl,
            cacheKey: $this->cacheKey
        );
    }
}

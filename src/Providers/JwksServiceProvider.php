<?php

declare(strict_types=1);

namespace JwksDecoder\Providers;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use JwksDecoder\Contracts\JwksDecoderInterface;
use JwksDecoder\Contracts\JwksProviderInterface;
use JwksDecoder\Services\JwksDecoder;
use JwksDecoder\Services\JwksProvider;

final class JwksServiceProvider extends ServiceProvider
{
    #[\Override]
    public function register(): void
    {
        $this->app->bind(JwksDecoderInterface::class, JwksDecoder::class);
        $this->app->bind(JwksProviderInterface::class, static function (Container $app): JwksProvider {
            $config = $app->make(ConfigRepository::class);

            return $app->make(JwksProvider::class, [
                'url' => $config->get('jwks-decoder.url'),
                'ttl' => (int) $config->get('jwks-decoder.cache_ttl'),
                'cacheKey' => $config->get('jwks-decoder.cache_key'),
            ]);
        });
    }

    public function boot(): void
    {
        $source = __DIR__ . '/../../config/jwks-decoder.php';

        if ($this->app->runningInConsole()) {
            $this->publishes([$source => config_path('jwks-decoder.php')]);
        }

        $this->mergeConfigFrom($source, 'jwks');
    }
}

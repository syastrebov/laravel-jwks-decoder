<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use Firebase\JWT\JWT;
use JwksDecoder\Services\JwksDecoder;
use Tests\TestCase;

final class JwksDecoderTest extends TestCase
{
    public function test_it_decodes_token_successfully(): void
    {
        $privateKeyResource = openssl_pkey_new([
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);

        openssl_pkey_export($privateKeyResource, $privateKey);
        $details = openssl_pkey_get_details($privateKeyResource);

        $jwks = [
            'keys' => [
                [
                    'kty' => 'RSA',
                    'alg' => 'RS256',
                    'use' => 'sig',
                    'kid' => 'test-key-id',
                    'n' => rtrim(strtr(base64_encode($details['rsa']['n']), '+/', '-_'), '='),
                    'e' => rtrim(strtr(base64_encode($details['rsa']['e']), '+/', '-_'), '='),
                ],
            ],
        ];

        $payload = [
            'sub' => 'user_123',
            'iat' => time(),
            'exp' => time() + 3600,
        ];

        $token = JWT::encode($payload, $privateKey, 'RS256', 'test-key-id');

        $decoder = new JwksDecoder();
        $result = $decoder->decodeToken($token, $jwks);

        $this->assertIsArray($result);
        $this->assertEquals('user_123', $result['sub']);
    }
}

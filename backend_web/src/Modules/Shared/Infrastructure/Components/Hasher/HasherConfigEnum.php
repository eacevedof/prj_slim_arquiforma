<?php

namespace App\Modules\Shared\Infrastructure\Components\Hasher;

/**
 * print_r(openssl_get_cipher_methods());
 */
final readonly class HasherConfigEnum
{
    public const ALG_AES_256_GCM = "AES-256-GCM";
    public const ALG_AES_128_GCM = "AES-128-GCM";
    public const ALG_AES_256_CCM = "AES-256-CCM";
    public const ALG_AES_256_CBC_HMAC_SHA256 = "AES-256-CBC-HMAC-SHA256";
    public const ALG_AES_256_CTR = "AES-256-CTR";

    public const ALG_AES_256_CBC = "AES-256-CBC";

    public const OPENSSL_BASE64 = 0;
    public const OPENSSL_RAW_DATA = 1;

    public const INITIALIZATION_VECTOR = "6a8cf75d922ad930c7a44158ac48d0cb37989185";

    public const ENCRYPT_SALT = "061c9060d8556766ed6f03e15222b529780c36c9";

    private function __construct() {}

}

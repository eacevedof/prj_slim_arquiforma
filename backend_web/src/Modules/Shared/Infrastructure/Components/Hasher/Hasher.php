<?php

declare(strict_types=1);

namespace App\Modules\Shared\Infrastructure\Components\Hasher;

use openssl_encrypt;
use openssl_decrypt;

final class Hasher
{
    private readonly string $encryptAlgorithm;
    private readonly string $initializationVector;
    private readonly string $encryptSalt;
    private readonly int $encodeAsRaw; // 1: raw data, 0: base64
    private ?string $aesBinHexTag = "";

    public function __construct(array $primitives = [])
    {
        $this->encryptAlgorithm = $primitives["encryptAlgorithm"] ?? HasherConfigEnum::AES_256_CBC;
        $this->initializationVector = $primitives["initializationVector"] ?? HasherConfigEnum::INITIALIZATION_VECTOR;
        $this->encryptSalt = $primitives["encryptSalt"] ?? HasherConfigEnum::ENCRYPT_SALT;
        $this->encodeAsRaw = $primitives["isRawData"] ?? HasherConfigEnum::OPENSSL_BASE64;
    }

    public static function getInstance(): self
    {
        return new self();
    }

    public static function fromPrimitives(array $primitives): self
    {
        return new self($primitives);
    }

    public function getEncryptedData(string $plaintext): string
    {
        $saltInSha1 = sha1($this->encryptSalt);
        $iniVector = $this->getInitialVector();
        return openssl_encrypt(
            $plaintext,
            $this->encryptAlgorithm,
            $saltInSha1,
            $this->encodeAsRaw,
            $iniVector,
            $this->aesBinHexTag
        );
    }

    public function getDecryptedData(string $encryptedText): string
    {
        $saltInSha1 = sha1($this->encryptSalt);
        $iniVector = $this->getInitialVector();
        return openssl_decrypt(
            $encryptedText,
            $this->encryptAlgorithm,
            $saltInSha1,
            $this->encodeAsRaw,
            $iniVector,
            $this->aesBinHexTag
        );
    }

    public function getAesDecryptedData(string $encryptedText, string $aesBinHexTag): string
    {
        $saltInSha1 = sha1($this->encryptSalt);
        $iniVector = $this->getInitialVector();
        return openssl_decrypt(
            $encryptedText,
            $this->encryptAlgorithm,
            $saltInSha1,
            $this->encodeAsRaw,
            $iniVector,
            $aesBinHexTag
        );
    }


    private function getInitialVector(): string
    {
        $iniVectorSha1 = sha1($this->initializationVector);
        return substr($iniVectorSha1, 0, 16);
    }

    public function doesPasswordMatch(string $hashedPassword, string $plainPassword): bool
    {
        return sodium_crypto_pwhash_str_verify($hashedPassword, $plainPassword);
    }

    public function getRandomStringByLength(int $length=12): string
    {
        $str = "45QWERTYU0123IOPASDFG78HJKLZXCVBNM69";
        $code = "";
        for ($a = 0; $a < $length; $a++) {
            $code .= $str[mt_rand(0, 35)];
        }
        return $code;
    }

    public function getSodiumEncrypted(string $plainText): string
    {
        return sodium_crypto_pwhash_str(
            $plainText,
            SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE,
            SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE
        );
    }

    public function getPackedToken(array $tokenPayload, string $groupSep = "φ", string $pairSep = "∮"): string
    {
        $payloadMapped = array_map(
            fn ($key, $value) => "{$key}{$pairSep}{$value}",
            array_keys($tokenPayload),
            $tokenPayload
        );
        $token = implode($groupSep, $payloadMapped);
        return base64_encode($token);
    }

    public function getUnpackedToken(string $base64token, string $groupSep = "φ", string $pairSep = "∮"): array
    {
        if (!$base64token) return [];

        $rawToken = base64_decode($base64token);
        $rawToken = explode($groupSep, $rawToken);

        $payloadMapped = [];
        foreach ($rawToken as $keyValue) {
            [$key, $value] = explode($pairSep, $keyValue);
            $payloadMapped[$key] = $value;
        }
        return $payloadMapped;
    }

    /**
     * @info cuando se usa aes este genera una una firma random para poder recuperar el texto
     * hay que pasarle la firma
     */
    public function getAesBinHexTag(): string
    {
        return $this->aesBinHexTag;
    }

}

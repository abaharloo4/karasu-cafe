<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Throwable;

class SafeEncryptedArray implements CastsAttributes
{
    /**
     * Cast the given value from database.
     * Supports:
     * 1. Encrypted container in JSON: {"_enc": "..."}
     * 2. Raw encrypted string: eyJpdiI6...
     * 3. Plaintext legacy JSON: {"merchant_id": "...", ...}
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): array
    {
        if (empty($value)) {
            return [];
        }

        // If already an array (e.g. from model cache or mock)
        if (is_array($value)) {
            if (isset($value['_enc'])) {
                try {
                    $decrypted = Crypt::decryptString($value['_enc']);
                    $inner = json_decode($decrypted, true);
                    return is_array($inner) ? $inner : [];
                } catch (Throwable) {
                    return [];
                }
            }
            return $value;
        }

        if (!is_string($value)) {
            return [];
        }

        // Try JSON decode first
        $decoded = json_decode($value, true);

        if (is_array($decoded)) {
            // Case 1: Encrypted container
            if (isset($decoded['_enc']) && is_string($decoded['_enc'])) {
                try {
                    $decrypted = Crypt::decryptString($decoded['_enc']);
                    $inner = json_decode($decrypted, true);
                    return is_array($inner) ? $inner : [];
                } catch (Throwable) {
                    return [];
                }
            }

            // Case 2: Plaintext array / legacy JSON
            return $decoded;
        }

        // Case 3: Raw encrypted string
        try {
            $decrypted = Crypt::decryptString($value);
            $inner = json_decode($decrypted, true);
            if (is_array($inner)) {
                return $inner;
            }
        } catch (Throwable) {
            // Not decryptable
        }

        return [];
    }

    /**
     * Prepare the given value for storage.
     * Encrypts the inner JSON and wraps in valid JSON container for MySQL JSON columns.
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        if (is_null($value)) {
            return json_encode([]);
        }

        $json = is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : (string)$value;
        $encrypted = Crypt::encryptString($json);

        return json_encode(['_enc' => $encrypted]);
    }
}

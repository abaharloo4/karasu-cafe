<?php

namespace Tests\Unit;

use App\Casts\SafeEncryptedArray;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class SafeEncryptedArrayTest extends TestCase
{
    private SafeEncryptedArray $cast;
    private Model $dummyModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cast = new SafeEncryptedArray();
        $this->dummyModel = new class extends Model {};
    }

    public function test_it_encrypts_array_into_wrapped_json_container(): void
    {
        $payload = [
            'merchant_id' => 'zarinpal-test-merchant-123',
            'api_key' => 'secret-key-xyz',
        ];

        $stored = $this->cast->set($this->dummyModel, 'settings', $payload, []);

        $decoded = json_decode($stored, true);
        $this->assertIsArray($decoded);
        $this->assertArrayHasKey('_enc', $decoded);

        // Verify the encrypted payload can be decrypted
        $decryptedJson = Crypt::decryptString($decoded['_enc']);
        $this->assertEquals($payload, json_decode($decryptedJson, true));
    }

    public function test_it_decrypts_wrapped_json_container_back_to_array(): void
    {
        $payload = ['card_number' => '6037-9918-1234-5678', 'bank_name' => 'Melli'];
        $encrypted = Crypt::encryptString(json_encode($payload));
        $dbValue = json_encode(['_enc' => $encrypted]);

        $result = $this->cast->get($this->dummyModel, 'settings', $dbValue, []);

        $this->assertEquals($payload, $result);
    }

    public function test_it_supports_legacy_plaintext_json_gracefully(): void
    {
        $legacy = ['merchant_id' => 'legacy-merchant', 'sandbox' => true];
        $dbValue = json_encode($legacy);

        $result = $this->cast->get($this->dummyModel, 'settings', $dbValue, []);

        $this->assertEquals($legacy, $result);
    }

    public function test_it_handles_null_and_empty_values_safely(): void
    {
        $this->assertEquals([], $this->cast->get($this->dummyModel, 'settings', null, []));
        $this->assertEquals([], $this->cast->get($this->dummyModel, 'settings', '', []));
        $this->assertEquals([], $this->cast->get($this->dummyModel, 'settings', 'invalid-corrupt-string', []));
    }
}

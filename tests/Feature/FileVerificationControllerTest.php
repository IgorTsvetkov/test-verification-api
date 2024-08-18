<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\UploadedFile;

class FileVerificationControllerTest extends TestCase
{
    const VALID_FILE_PATH = 'tests/External/verifiable-file.json';

    public function test_valid_issuer_should_present_in_input_file(): void
    {
        $user = User::factory()->create();
        $filePath = base_path(self::VALID_FILE_PATH);
        $jsonContent = file_get_contents($filePath);
        $jsonContent = json_decode($jsonContent, JSON_OBJECT_AS_ARRAY);

        //unset issuer to test the validation for issuer
        unset($jsonContent['data']['issuer']['name']);
        $jsonContent = json_encode($jsonContent);
        $file = UploadedFile::fake()->createWithContent('invalid-issuer-test.json', $jsonContent);
        
        $response = $this->actingAs($user)->post('api/files/verification', [
            'file' => $file,
        ]);
        
        $response->assertStatus(200);
        
        $response->assertJson([
            'data' => [
                'result' => 'invalid_issuer',
            ],
        ]);

        $this->assertDatabaseHas('verifications', [
            'user_id' => $user->id,
            'file_type' => 'json',
            'result_status' => 'invalid_issuer',
        ]);
    }

    public function test_valid_recipient_should_present_in_input_file(): void
    {
        $user = User::factory()->create();
        $filePath = base_path(self::VALID_FILE_PATH);
        $jsonContent = file_get_contents($filePath);
        $jsonContent = json_decode($jsonContent, JSON_OBJECT_AS_ARRAY);

        //unset recipient to test the validation for recipient
        unset($jsonContent['data']['recipient']);
        $jsonContent = json_encode($jsonContent);
        $file = UploadedFile::fake()->createWithContent('invalid-issuer-test.json', $jsonContent);
        
        $response = $this->actingAs($user)->post('api/files/verification', [
            'file' => $file,
        ]);
        
        $response->assertStatus(200);
        
        $response->assertJson([
            'data' => [
                'result' => 'invalid_recipient',
            ],
        ]);

        $this->assertDatabaseHas('verifications', [
            'user_id' => $user->id,
            'file_type' => 'json',
            'result_status' => 'invalid_recipient',
        ]);
    }

    public function test_valid_signature_should_present_in_input_data(): void
    {
        $user = User::factory()->create();
        $filePath = base_path(self::VALID_FILE_PATH);
        $jsonContent = file_get_contents($filePath);
        $jsonContent = json_decode($jsonContent, JSON_OBJECT_AS_ARRAY);

        //set invalid signature to test that it returns invalid_signature
        $jsonContent['signature']['targetHash'] = 'invalid-signature-example';
        $jsonContent = json_encode($jsonContent);
        $file = UploadedFile::fake()->createWithContent('invalid-issuer-test.json', $jsonContent);
        
        $response = $this->actingAs($user)->post('api/files/verification', [
            'file' => $file,
        ]);
        
        $response->assertStatus(200);
        
        $response->assertJson([
            'data' => [
                'result' => 'invalid_signature',
            ],
        ]);

        $this->assertDatabaseHas('verifications', [
            'user_id' => $user->id,
            'file_type' => 'json',
            'result_status' => 'invalid_signature',
        ]);
    }

    public function test_valid_file_returns_valid_response(): void
    {
        $user = User::factory()->create();

        $filePath = base_path(self::VALID_FILE_PATH);
        $jsonContent = file_get_contents($filePath);
        $file = UploadedFile::fake()->createWithContent('verifiable-file.json', $jsonContent);

        $response = $this->actingAs($user)->post('api/files/verification', [
            'file' => $file,
        ]);

        $response->assertStatus(200);

        $response->assertJson([
            'data' => [
                'result' => 'verified',
                'issuer' => 'Accredify',
            ],
        ]);

        $this->assertDatabaseHas('verifications', [
            'user_id' => $user->id,
            'file_type' => 'json',
            'result_status' => 'verified',
            'issuer_name' => 'Accredify',
        ]);
    }
}

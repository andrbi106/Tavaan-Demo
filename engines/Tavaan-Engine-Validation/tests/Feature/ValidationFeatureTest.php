<?php

declare(strict_types=1);

namespace Tavaan\Engine\Validation\Tests\Feature;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Validation\Validator;
use Tavaan\Engine\Validation\ValidationServiceProvider;

final class ValidationFeatureTest extends TestCase
{
    public function test_service_provider_provides(): void
    {
        $provider = new ValidationServiceProvider([]);
        $this->assertContains(Validator::class, $provider->provides());
    }

    public function test_config_file_loads(): void
    {
        $config = require __DIR__ . '/../../config/validation.php';
        $this->assertArrayHasKey('strict', $config);
        $this->assertArrayHasKey('stop_on_first', $config);
    }

    public function test_lang_fa_loads(): void
    {
        $messages = require __DIR__ . '/../../lang/fa/validation.php';
        $this->assertArrayHasKey('error_required', $messages);
        $this->assertArrayHasKey('error_email', $messages);
    }

    public function test_lang_en_loads(): void
    {
        $messages = require __DIR__ . '/../../lang/en/validation.php';
        $this->assertArrayHasKey('error_required', $messages);
    }

    public function test_end_to_end_user_registration_validation(): void
    {
        $rules = [
            'name' => ['required', 'string', 'min:3', 'max:50'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'mobile' => ['required', 'iran_mobile'],
            'age' => ['required', 'integer', 'between:18,120'],
        ];

        $valid = Validator::make([
            'name' => 'Ali',
            'email' => 'ali@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'mobile' => '09121234567',
            'age' => 25,
        ], $rules);
        $this->assertTrue($valid->passes());

        $invalid = Validator::make([
            'name' => 'A',
            'email' => 'not-email',
            'password' => 'short',
            'password_confirmation' => 'different',
            'mobile' => '12345',
            'age' => 200,
        ], $rules);
        $this->assertTrue($invalid->fails());
        $errors = $invalid->errors();
        $this->assertArrayHasKey('name', $errors);
        $this->assertArrayHasKey('email', $errors);
        $this->assertArrayHasKey('password', $errors);
        $this->assertArrayHasKey('mobile', $errors);
        $this->assertArrayHasKey('age', $errors);
    }
}

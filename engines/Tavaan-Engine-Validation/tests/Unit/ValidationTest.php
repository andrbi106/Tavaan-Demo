<?php

declare(strict_types=1);

namespace Tavaan\Engine\Validation\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Validation\Validator;
use Tavaan\Engine\Validation\Rules\Required;
use Tavaan\Engine\Validation\Rules\Email;
use Tavaan\Engine\Validation\Rules\Min;
use Tavaan\Engine\Validation\Rules\Max;
use Tavaan\Engine\Validation\Rules\Between;
use Tavaan\Engine\Validation\Rules\In;
use Tavaan\Engine\Validation\Rules\Integer;
use Tavaan\Engine\Validation\Rules\StringType;
use Tavaan\Engine\Validation\Rules\Numeric;
use Tavaan\Engine\Validation\Rules\BooleanType;
use Tavaan\Engine\Validation\Rules\ArrayType;
use Tavaan\Engine\Validation\Rules\Confirmed;
use Tavaan\Engine\Validation\Rules\Regex;
use Tavaan\Engine\Validation\Rules\IranMobile;
use Tavaan\Engine\Validation\Rules\Nullable;
use Tavaan\Engine\Validation\Exceptions\ValidationException;
use Tavaan\Engine\Validation\Contracts\RuleInterface;

final class ValidationTest extends TestCase
{
    public function test_required_passes_with_value(): void
    {
        $rule = new Required();
        $this->assertTrue($rule->passes('x', 'value', ['x' => 'value']));
    }

    public function test_required_fails_with_empty_string(): void
    {
        $rule = new Required();
        $this->assertFalse($rule->passes('x', '', ['x' => '']));
    }

    public function test_required_fails_with_null(): void
    {
        $rule = new Required();
        $this->assertFalse($rule->passes('x', null, ['x' => null]));
    }

    public function test_required_fails_with_empty_array(): void
    {
        $rule = new Required();
        $this->assertFalse($rule->passes('x', [], ['x' => []]));
    }

    public function test_email_valid(): void
    {
        $rule = new Email();
        $this->assertTrue($rule->passes('x', 'user@example.com', ['x' => 'user@example.com']));
    }

    public function test_email_invalid(): void
    {
        $rule = new Email();
        $this->assertFalse($rule->passes('x', 'not-an-email', ['x' => 'not-an-email']));
    }

    public function test_min_string_length(): void
    {
        $rule = new Min(3);
        $this->assertTrue($rule->passes('x', 'hello', []));
        $this->assertFalse($rule->passes('x', 'hi', []));
    }

    public function test_min_numeric(): void
    {
        $rule = new Min(10);
        $this->assertTrue($rule->passes('x', 15, []));
        $this->assertFalse($rule->passes('x', 5, []));
    }

    public function test_max_string_length(): void
    {
        $rule = new Max(3);
        $this->assertTrue($rule->passes('x', 'hi', []));
        $this->assertFalse($rule->passes('x', 'hello', []));
    }

    public function test_between(): void
    {
        $rule = new Between(2, 5);
        $this->assertTrue($rule->passes('x', 'abc', []));
        $this->assertTrue($rule->passes('x', 'ab', []));
        $this->assertFalse($rule->passes('x', 'a', []));
        $this->assertFalse($rule->passes('x', 'abcdef', []));
    }

    public function test_in(): void
    {
        $rule = new In(['active', 'inactive']);
        $this->assertTrue($rule->passes('x', 'active', []));
        $this->assertFalse($rule->passes('x', 'pending', []));
    }

    public function test_integer_valid(): void
    {
        $rule = new Integer();
        $this->assertTrue($rule->passes('x', 42, []));
        $this->assertTrue($rule->passes('x', '42', []));
        $this->assertFalse($rule->passes('x', '4.5', []));
        $this->assertFalse($rule->passes('x', 'abc', []));
    }

    public function test_string_type(): void
    {
        $rule = new StringType();
        $this->assertTrue($rule->passes('x', 'hello', []));
        $this->assertFalse($rule->passes('x', 42, []));
    }

    public function test_numeric(): void
    {
        $rule = new Numeric();
        $this->assertTrue($rule->passes('x', 42, []));
        $this->assertTrue($rule->passes('x', '4.5', []));
        $this->assertFalse($rule->passes('x', 'abc', []));
    }

    public function test_boolean_type(): void
    {
        $rule = new BooleanType();
        $this->assertTrue($rule->passes('x', true, []));
        $this->assertTrue($rule->passes('x', 0, []));
        $this->assertFalse($rule->passes('x', 'not_bool', []));
    }

    public function test_array_type(): void
    {
        $rule = new ArrayType();
        $this->assertTrue($rule->passes('x', [1, 2], []));
        $this->assertFalse($rule->passes('x', 'string', []));
    }

    public function test_confirmed(): void
    {
        $rule = new Confirmed();
        $this->assertTrue($rule->passes('password', 'secret', ['password' => 'secret', 'password_confirmation' => 'secret']));
        $this->assertFalse($rule->passes('password', 'secret', ['password' => 'secret', 'password_confirmation' => 'different']));
    }

    public function test_regex(): void
    {
        $rule = new Regex('/^\d{4}$/');
        $this->assertTrue($rule->passes('x', '1234', []));
        $this->assertFalse($rule->passes('x', 'abc', []));
    }

    public function test_iran_mobile_valid(): void
    {
        $rule = new IranMobile();
        $this->assertTrue($rule->passes('x', '09121234567', []));
        $this->assertFalse($rule->passes('x', '0912123456', []));
        $this->assertFalse($rule->passes('x', '08121234567', []));
    }

    // === Validator ===

    public function test_validator_passes_with_valid_data(): void
    {
        $v = Validator::make(
            ['email' => 'user@example.com', 'age' => 25],
            ['email' => ['required', 'email'], 'age' => ['required', 'integer']]
        );
        $this->assertTrue($v->passes());
        $this->assertFalse($v->fails());
    }

    public function test_validator_fails_with_invalid_data(): void
    {
        $v = Validator::make(
            ['email' => 'not-email', 'age' => 'abc'],
            ['email' => ['required', 'email'], 'age' => ['required', 'integer']]
        );
        $this->assertTrue($v->fails());
    }

    public function test_validator_errors(): void
    {
        $v = Validator::make(
            ['email' => 'invalid'],
            ['email' => ['required', 'email']]
        );
        $errors = $v->errors();
        $this->assertArrayHasKey('email', $errors);
    }

    public function test_validator_required_fails(): void
    {
        $v = Validator::make(
            [],
            ['name' => ['required']]
        );
        $this->assertTrue($v->fails());
        $this->assertArrayHasKey('name', $v->errors());
    }

    public function test_validator_min_string(): void
    {
        $v = Validator::make(
            ['name' => 'ab'],
            ['name' => ['required', 'min:3']]
        );
        $this->assertTrue($v->fails());
    }

    public function test_validator_max_string(): void
    {
        $v = Validator::make(
            ['name' => 'hello world'],
            ['name' => ['required', 'max:5']]
        );
        $this->assertTrue($v->fails());
    }

    public function test_validator_between(): void
    {
        $v = Validator::make(
            ['age' => 50],
            ['age' => ['required', 'integer', 'between:18,120']]
        );
        $this->assertTrue($v->passes());

        $v2 = Validator::make(
            ['age' => 200],
            ['age' => ['required', 'integer', 'between:18,120']]
        );
        $this->assertTrue($v2->fails());
    }

    public function test_validator_in(): void
    {
        $v = Validator::make(
            ['status' => 'active'],
            ['status' => ['required', 'in:active,inactive']]
        );
        $this->assertTrue($v->passes());

        $v2 = Validator::make(
            ['status' => 'pending'],
            ['status' => ['required', 'in:active,inactive']]
        );
        $this->assertTrue($v2->fails());
    }

    public function test_validator_confirmed(): void
    {
        $v = Validator::make(
            ['password' => 'secret', 'password_confirmation' => 'secret'],
            ['password' => ['required', 'confirmed']]
        );
        $this->assertTrue($v->passes());

        $v2 = Validator::make(
            ['password' => 'secret', 'password_confirmation' => 'different'],
            ['password' => ['required', 'confirmed']]
        );
        $this->assertTrue($v2->fails());
    }

    public function test_validator_iran_mobile(): void
    {
        $v = Validator::make(
            ['mobile' => '09121234567'],
            ['mobile' => ['required', 'iran_mobile']]
        );
        $this->assertTrue($v->passes());

        $v2 = Validator::make(
            ['mobile' => 'invalid'],
            ['mobile' => ['required', 'iran_mobile']]
        );
        $this->assertTrue($v2->fails());
    }

    public function test_validator_nullable_skips_other_rules(): void
    {
        $v = Validator::make(
            ['nickname' => null],
            ['nickname' => ['nullable', 'string', 'min:3']]
        );
        $this->assertTrue($v->passes());
    }

    public function test_validator_custom_message(): void
    {
        $v = Validator::make(
            ['email' => 'invalid'],
            ['email' => ['required', 'email']],
            ['email' => 'ایمیل اشتباه است.']
        );
        $errors = $v->errors();
        $this->assertSame('ایمیل اشتباه است.', $errors['email'][0]);
    }

    public function test_validator_custom_attribute_name(): void
    {
        $v = Validator::make(
            ['name' => null],
            ['name' => ['required']],
            [],
            ['name' => 'نام کاربری']
        );
        $errors = $v->errors();
        $this->assertStringContainsString('نام کاربری', $errors['name'][0]);
    }

    public function test_validator_validate_throws_on_failure(): void
    {
        $v = Validator::make(
            ['email' => 'invalid'],
            ['email' => ['required', 'email']]
        );
        $this->expectException(ValidationException::class);
        $v->validate();
    }

    public function test_validator_validate_returns_data(): void
    {
        $v = Validator::make(
            ['email' => 'user@example.com', 'extra' => 'ignored'],
            ['email' => ['required', 'email']]
        );
        $data = $v->validate();
        $this->assertSame(['email' => 'user@example.com'], $data);
    }

    public function test_validator_all_errors(): void
    {
        $v = Validator::make(
            ['email' => 'invalid', 'age' => 'abc'],
            ['email' => ['required', 'email'], 'age' => ['required', 'integer']]
        );
        $all = $v->allErrors();
        $this->assertGreaterThanOrEqual(2, \count($all));
    }

    public function test_validator_with_custom_rule_object(): void
    {
        $v = Validator::make(
            ['value' => 'test'],
            ['value' => [new class implements RuleInterface {
                public function passes(string $a, mixed $value, array $data): bool { return $value === 'test'; }
                public function message(): string { return 'Custom rule failed.'; }
            }]]
        );
        $this->assertTrue($v->passes());
    }

    public function test_rule_interface_implemented_by_all(): void
    {
        $rules = [
            new Required(), new Email(), new Min(1), new Max(1), new Between(1, 2),
            new In(['a']), new Integer(), new StringType(), new Numeric(),
            new BooleanType(), new ArrayType(), new Confirmed(), new Regex('//'),
            new IranMobile(), new Nullable(),
        ];
        foreach ($rules as $r) {
            $this->assertInstanceOf(RuleInterface::class, $r);
        }
    }
}

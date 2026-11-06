<?php

declare(strict_types=1);

namespace Tavaan\Engine\Validation\Testing;

use Tavaan\Engine\Validation\Rules\Between;
use Tavaan\Engine\Validation\Rules\Email;
use Tavaan\Engine\Validation\Rules\Integer;
use Tavaan\Engine\Validation\Rules\IranMobile;
use Tavaan\Engine\Validation\Rules\MaxLength;
use Tavaan\Engine\Validation\Rules\Required;

/**
 * تَوان | Tavaan — Validation
 * CreateUserDto — Fixture تست Attribute-based validation (DOC-051 §6).
 *
 * @api internal
 * @see docs/03-engines/validation.md
 */
final class CreateUserDto
{
    public function __construct(
        #[Required]
        #[Email]
        #[MaxLength(255)]
        public readonly string $email,
        #[Required]
        #[Integer]
        #[Between(18, 120)]
        public readonly int $age,
        #[Required]
        #[IranMobile]
        public readonly string $mobile,
    ) {
    }
}

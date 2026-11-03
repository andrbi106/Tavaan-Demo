<?php

declare(strict_types=1);

namespace Tavaan\Engine\Security;

final class Csrf
{
    private string $token;

    public function __construct(?string $token = null)
    {
        $this->token = $token ?? bin2hex(random_bytes(32));
    }

    public function token(): string
    {
        return $this->token;
    }

    public function field(): string
    {
        return '<input type="hidden" name="_token" value="' . $this->token . '">';
    }

    public function rotate(): void
    {
        $this->token = bin2hex(random_bytes(32));
    }

    public function verify(string $token): bool
    {
        return hash_equals($this->token, $token);
    }
}

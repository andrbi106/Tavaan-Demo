<?php

declare(strict_types=1);

namespace Tavaan\Engine\Validation;

abstract class FormRequest
{
    protected array $data = [];
    protected ?Validator $validator = null;
    protected bool $passed = false;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function authorize(): bool
    {
        return true;
    }

    abstract public function rules(): array;

    public function messages(): array
    {
        return [];
    }

    public function attributes(): array
    {
        return [];
    }

    public function validated(): array
    {
        if (!$this->passed) {
            throw new Exceptions\ValidationException($this->validator?->errors() ?? []);
        }
        return array_intersect_key($this->data, $this->rules());
    }

    public function validateResolved(): void
    {
        if (!$this->authorize()) {
            throw new \RuntimeException('Unauthorized.');
        }
        $this->validator = Validator::make(
            $this->data,
            $this->rules(),
            $this->messages(),
            $this->attributes()
        );
        $this->passed = $this->validator->passes();
        if (!$this->passed) {
            throw new Exceptions\ValidationException($this->validator->errors());
        }
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function merge(array $data): self
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }
}

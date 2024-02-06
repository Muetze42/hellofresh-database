<?php

namespace App\ValueObjects;

use Illuminate\Database\Eloquent\Concerns\HasAttributes;

abstract class AbstractObject
{
    use HasAttributes;

    public array $data;

    public function __construct(?string $value)
    {
        $this->data = $this->fromJson($value);

        if ($this->data) {
            $this->data = $this->castData();
        }
    }

    public function toArray(): array
    {
        return $this->data;
    }

    abstract protected function castData(): array;
}

<?php

declare(strict_types=1);

namespace App\Processor;

interface ProcessorInterface
{
    public const CREATE = 'create';
    public const UPDATE = 'update';
    public const DELETE = 'delete';

    public function process(mixed $data, string $operation, int|string|null $id = null): mixed;
}

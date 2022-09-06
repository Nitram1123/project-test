<?php

declare(strict_types=1);

namespace App\Provider;

interface ProviderInterface
{
    public const READ   = 'read';
    public const SEARCH = 'search';

    public function provide(mixed $data, string $operation, int|string|null $id = null): mixed;
}

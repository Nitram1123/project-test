<?php

declare(strict_types=1);

namespace App\Query;

interface QueryBusInterface
{
    public function ask(QueryInterface $query): mixed;
}

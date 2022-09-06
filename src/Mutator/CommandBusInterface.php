<?php

declare(strict_types=1);

namespace App\Mutator;

interface CommandBusInterface
{
    public function dispatch(CommandInterface $command): mixed;
}

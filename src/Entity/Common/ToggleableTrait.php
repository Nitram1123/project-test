<?php

declare(strict_types=1);

namespace App\Entity\Common;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait ToggleableTrait
{
    #[ORM\Column(type: 'boolean', options: ['default' => 0])]
    #[Groups(['item:read'])]
    protected bool $enabled = true;

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Alias for {@see enabled} in order to work with Serializer in API Platform.
     */
    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled = true): self
    {
        $this->enabled = $enabled;

        return $this;
    }
}

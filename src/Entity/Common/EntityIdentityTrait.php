<?php

declare(strict_types=1);

namespace App\Entity\Common;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait EntityIdentityTrait
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    #[ORM\GeneratedValue]
    #[Groups(['entity_id'])]
    protected int|null $id = null;

    /**
     * Called by the Symfony Serializer when denormalizing data.
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): int|null
    {
        return $this->id;
    }

    public function hasId(): bool
    {
        return \property_exists($this, 'id') && $this->id !== null;
    }
}

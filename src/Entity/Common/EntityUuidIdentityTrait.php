<?php

declare(strict_types=1);

namespace App\Entity\Common;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

trait EntityUuidIdentityTrait
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy:'CUSTOM')] // @phpstan-ignore-line
    #[ORM\CustomIdGenerator('doctrine.uuid_generator')] // @phpstan-ignore-line
    #[Groups(['entity_uuid'])]
    protected Uuid $id;

    /**
     * Called by the Symfony Serializer when denormalizing data.
     */
    public function setId(string|Uuid $id): self
    {
        if (\is_string($id)) {
            $id = Uuid::fromString($id);
        }

        if (! $id instanceof Uuid) {
            throw new \InvalidArgumentException('Id must be a UUID');
        }

        $this->id = $id;

        return $this;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }
}

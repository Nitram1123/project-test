<?php

declare(strict_types=1);

namespace App\Entity\Common;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Length;

trait SluggableTrait
{
    #[ORM\Column(nullable: true)]
    #[Length(max: 255)]
    protected string|null $slug = null;

    public function getSlug(): string|null
    {
        return $this->slug;
    }

    public function setSlug(string|null $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}

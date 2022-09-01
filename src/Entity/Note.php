<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Common\EntityUuidIdentityTrait;
use App\Entity\Common\TimestampableTrait;
use App\Repository\NoteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: NoteRepository::class)]
#[ApiResource(
    collectionOperations: ['post' => ['security_post_denormalize' => "is_granted('NOTE_NEW', object)"]],
    itemOperations: ['get'],
)]
class Note
{
    use EntityUuidIdentityTrait;
    use TimestampableTrait;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\Range(
        min: 0,
        max: 5,
    )]

    private int|null $rate = null;

    #[ORM\ManyToOne(inversedBy: 'notes')]
    #[ORM\JoinColumn(nullable: false)]
    private Member|null $author = null;

    #[ORM\ManyToOne(inversedBy: 'notes')]
    private Comment|null $comment = null;

    public function getRate(): int|null
    {
        return $this->rate;
    }

    public function setRate(int $rate): self
    {
        $this->rate = $rate;

        return $this;
    }

    public function getAuthor(): Member|null
    {
        return $this->author;
    }

    public function setAuthor(Member|null $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getComment(): Comment|null
    {
        return $this->comment;
    }

    public function setComment(Comment|null $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}

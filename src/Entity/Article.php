<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Common\EntityUuidIdentityTrait;
use App\Entity\Common\TimestampableTrait;
use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [ 'method' => 'get' ],
        'post' => [ 'security' => "is_granted('ROLE_ADMIN')" ],
    ],
    itemOperations: [
        'get' => [ 'method' => 'get' ],
        'put' => [ 'security' => "is_granted('ROLE_ADMIN')" ],
        'delete' => [ 'security' => "is_granted('ROLE_ADMIN')" ],
    ],
)]
class Article
{
    use EntityUuidIdentityTrait;
    use TimestampableTrait;

    #[ORM\Column]
    private bool $published = false;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private string|null $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    private string|null $content = null;

    #[ORM\ManyToOne(inversedBy: 'articles', cascade:['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private Member|null $author = null;

    /** @var Collection<int, Comment> */
    #[ORM\OneToMany(mappedBy: 'article', targetEntity: Comment::class, cascade:['persist'])]
    private Collection $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function isPublished(): bool|null
    {
        return $this->published;
    }

    public function setPublished(bool $published): self
    {
        $this->published = $published;

        return $this;
    }

    public function getTitle(): string|null
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): string|null
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

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

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (! $this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setArticle($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getArticle() === $this) {
                $comment->setArticle(null);
            }
        }

        return $this;
    }
}

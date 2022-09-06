<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Common\EntityUuidIdentityTrait;
use App\Entity\Common\TimestampableTrait;
use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    use EntityUuidIdentityTrait;
    use TimestampableTrait;

    #[ORM\Column]
    #[Groups(['article:read'])]
    private bool $published = false;

    #[ORM\Column(length: 255)]
    #[Groups(['article:read'])]
    private string $title;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['article:read'])]
    private string $content;

    #[ORM\ManyToOne(inversedBy: 'articles', cascade:['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['article:read'])]
    private Member $author;

    /** @var Collection<int, Comment> */
    #[ORM\OneToMany(mappedBy: 'article', targetEntity: Comment::class, cascade:['persist'])]
    private Collection $comments;

    public function __construct(string $title, string $content, Member $author)
    {
        $this->title    = $title;
        $this->content  = $content;
        $this->author   = $author;
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

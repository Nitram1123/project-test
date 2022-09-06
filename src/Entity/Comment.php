<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Common\EntityUuidIdentityTrait;
use App\Entity\Common\TimestampableTrait;
use App\Entity\Common\ToggleableTrait;
use App\Repository\CommentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    use EntityUuidIdentityTrait;
    use TimestampableTrait;
    use ToggleableTrait;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['comment:read'])]
    private string|null $content = null;

    #[ORM\ManyToOne(inversedBy: 'comments', cascade:['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['comment:read'])]
    private Member|null $author = null;

    #[ORM\ManyToOne(inversedBy: 'comments', cascade:['persist'])]
    #[Groups(['comment:read'])]
    private Article|null $article = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'comments', cascade:['persist'])]
    #[Groups(['comment:read'])]
    private self|null $parent = null;

    /** @var Collection<int, Comment> */
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class, cascade:['persist', 'remove'])]
    private Collection $comments;

    /** @var Collection<int, Note> */
    #[ORM\OneToMany(mappedBy: 'comment', targetEntity: Note::class, cascade:['persist'])]
    private Collection $notes;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->notes    = new ArrayCollection();
        $this->enabled  = false;
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

    public function getArticle(): Article|null
    {
        return $this->article;
    }

    public function setArticle(Article|null $article): self
    {
        $this->article = $article;

        return $this;
    }

    public function getParent(): self|null
    {
        return $this->parent;
    }

    public function setParent(self|null $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(self $comment): self
    {
        if (! $this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setParent($this);
        }

        return $this;
    }

    public function removeComment(self $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getParent() === $this) {
                $comment->setParent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Note>
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): self
    {
        if (! $this->notes->contains($note)) {
            $this->notes->add($note);
            $note->setComment($this);
        }

        return $this;
    }

    public function removeNote(Note $note): self
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getComment() === $this) {
                $note->setComment(null);
            }
        }

        return $this;
    }
}

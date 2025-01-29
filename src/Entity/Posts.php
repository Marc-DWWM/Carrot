<?php

namespace App\Entity;

use App\Repository\PostsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostsRepository::class)]
class Posts
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $content = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'reposts')]
    #[ORM\JoinColumn(name: 'original_post_id', referencedColumnName: 'id')]
    private ?self $originalPost = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'originalPost')]
    private Collection $reposts;

    public function __construct(User $author)
    {
        $this->author = $author;
        $this->created_at = new \DateTimeImmutable();
        $this->reposts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getOriginalPost(): ?self
    {
        return $this->originalPost;
    }

    public function setOriginalPost(?self $originalPost): static
    {
        $this->originalPost = $originalPost;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getReposts(): Collection
    {
        return $this->reposts;
    }

    public function addRepost(self $repost): static
    {
        if (!$this->reposts->contains($repost)) {
            $this->reposts->add($repost);
            $repost->setOriginalPost($this);
        }

        return $this;
    }

    public function removeRepost(self $repost): static
    {
        if ($this->reposts->removeElement($repost)) {
            // set the owning side to null (unless already changed)
            if ($repost->getOriginalPost() === $this) {
                $repost->setOriginalPost(null);
            }
        }

        return $this;
    }
}

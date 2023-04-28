<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\Column(length: 255)]
    private ?string $mediaTitle = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\ManyToMany(targetEntity: Article::class, mappedBy: 'images')]
    private Collection $articles;

    #[ORM\ManyToMany(targetEntity: Product::class, mappedBy: 'images')]
    private Collection $products;

    #[ORM\Column(length: 255)]
    private ?string $imageFile = null;

    #[ORM\Column(length: 255)]
    private ?string $caption = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $mediaContent = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $urlLink = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $urlTitle = null;

    #[ORM\Column(nullable: true)]
    private ?int $priorityOrder = null;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
        $this->products = new ArrayCollection();
    }

    /**
     * Retourne le nom de l'objet Image sous forme d'une chaîne de caractères
     *
     * @return string
    */
    public function __toString()
    {
        return $this->mediaTitle;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->addImage($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->removeElement($article)) {
            $article->removeImage($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->addImage($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            $product->removeImage($this);
        }

        return $this;
    }

    public function getImageFile(): ?string
    {
        return $this->imageFile;
    }

    public function setImageFile(string $imageFile): self
    {
        $this->imageFile = $imageFile;

        return $this;
    }

    public function getCaption(): ?string
    {
        return $this->caption;
    }

    public function setCaption(string $caption): self
    {
        $this->caption = $caption;

        return $this;
    }

    public function getMediaTitle(): ?string
    {
        return $this->mediaTitle;
    }

    public function setMediaTitle(string $mediaTitle): self
    {
        $this->mediaTitle = $mediaTitle;

        return $this;
    }

    public function getMediaContent(): ?string
    {
        return $this->mediaContent;
    }

    public function setMediaContent(string $mediaContent): self
    {
        $this->mediaContent = $mediaContent;

        return $this;
    }

    public function getUrlLink(): ?string
    {
        return $this->urlLink;
    }

    public function setUrlLink(?string $urlLink): self
    {
        $this->urlLink = $urlLink;

        return $this;
    }

    public function getUrlTitle(): ?string
    {
        return $this->urlTitle;
    }

    public function setUrlTitle(?string $urlTitle): self
    {
        $this->urlTitle = $urlTitle;

        return $this;
    }

    public function getPriorityOrder(): ?int
    {
        return $this->priorityOrder;
    }

    public function setPriorityOrder(?int $priorityOrder): self
    {
        $this->priorityOrder = $priorityOrder;

        return $this;
    }
}

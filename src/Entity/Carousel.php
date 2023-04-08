<?php

namespace App\Entity;

use App\Repository\CarouselRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CarouselRepository::class)]
class Carousel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column(length: 255)]
    private ?string $buttonTitle = null;

    #[ORM\Column(length: 255)]
    private ?string $buttonUrl = null;

    #[ORM\Column(length: 255)]
    private ?string $coverImage = null;

    #[ORM\Column(nullable: true)]
    private ?int $priorityOrder = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getButtonTitle(): ?string
    {
        return $this->buttonTitle;
    }

    public function setButtonTitle(string $buttonTitle): self
    {
        $this->buttonTitle = $buttonTitle;

        return $this;
    }

    public function getButtonUrl(): ?string
    {
        return $this->buttonUrl;
    }

    public function setButtonUrl(string $buttonUrl): self
    {
        $this->buttonUrl = $buttonUrl;

        return $this;
    }

    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    public function setCoverImage(string $coverImage): self
    {
        $this->coverImage = $coverImage;

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

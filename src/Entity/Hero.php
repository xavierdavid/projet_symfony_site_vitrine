<?php

namespace App\Entity;

use App\Repository\HeroRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HeroRepository::class)]
class Hero
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $slogan = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column(length: 255)]
    private ?string $firstButtonTitle = null;

    #[ORM\Column(length: 255)]
    private ?string $firstButtonUrl = null;

    #[ORM\Column(length: 255)]
    private ?string $secondButtonTitle = null;

    #[ORM\Column(length: 255)]
    private ?string $secondButtonUrl = null;

    #[ORM\Column(length: 255)]
    private ?string $masterImage = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $masterImageDescription = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlogan(): ?string
    {
        return $this->slogan;
    }

    public function setSlogan(string $slogan): static
    {
        $this->slogan = $slogan;

        return $this;
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

    public function getFirstButtonTitle(): ?string
    {
        return $this->firstButtonTitle;
    }

    public function setFirstButtonTitle(string $firstButtonTitle): static
    {
        $this->firstButtonTitle = $firstButtonTitle;

        return $this;
    }

    public function getFirstButtonUrl(): ?string
    {
        return $this->firstButtonUrl;
    }

    public function setFirstButtonUrl(string $firstButtonUrl): static
    {
        $this->firstButtonUrl = $firstButtonUrl;

        return $this;
    }

    public function getSecondButtonTitle(): ?string
    {
        return $this->secondButtonTitle;
    }

    public function setSecondButtonTitle(string $secondButtonTitle): static
    {
        $this->secondButtonTitle = $secondButtonTitle;

        return $this;
    }

    public function getSecondButtonUrl(): ?string
    {
        return $this->secondButtonUrl;
    }

    public function setSecondButtonUrl(string $secondButtonUrl): static
    {
        $this->secondButtonUrl = $secondButtonUrl;

        return $this;
    }

    public function getMasterImage(): ?string
    {
        return $this->masterImage;
    }

    public function setMasterImage(string $masterImage): static
    {
        $this->masterImage = $masterImage;

        return $this;
    }

    public function getMasterImageDescription(): ?string
    {
        return $this->masterImageDescription;
    }

    public function setMasterImageDescription(?string $masterImageDescription): static
    {
        $this->masterImageDescription = $masterImageDescription;

        return $this;
    }
}

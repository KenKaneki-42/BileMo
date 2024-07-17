<?php

declare(strict_types=1);


namespace App\Entity;

use App\Repository\PhoneRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation(
 *    "self",
 *    href = @Hateoas\Route(
 *        "phone_details",
 *        parameters = { "id" = "expr(object.getId())" }
 *    ),
 * )
 */

#[ORM\Entity(repositoryClass: PhoneRepository::class)]

class Phone
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 50, message: "Votre model doit contenir entre 2 et 50 caractères.")]
    #[Assert\Regex(pattern: "/^[a-zA-ZÀ-ÿ '-]+$/u", message: "Votre model ne peut contenir que des lettres.")]
    private ?string $model = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 50, message: "Le nom de la marque doit contenir entre 2 et 50 caractères.")]
    #[Assert\Regex(pattern: "/^[a-zA-ZÀ-ÿ '-]+$/u", message: "Le nom de la marque ne peut contenir que des lettres.")]
    private ?string $brand = null;

    #[ORM\Column(type: Types::TEXT, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 18, max: 800, minMessage: "La description doit contenir au moins 18 caractères.", maxMessage: "La description ne peut pas contenir plus de 800 caractères.")]
    private ?string $description = null;

    #[ORM\Column(type: Types::FLOAT, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Positive]
    private ?float $price = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\LessThanOrEqual(value: "today", message: "La date de création ne peut pas être dans le futur.")]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\LessThanOrEqual(value: "today", message: "La date de mise à jour ne peut pas être dans le futur.")]
    private ?\DateTime $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}

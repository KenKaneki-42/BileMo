<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column(type: Types::INTEGER)]
  private ?int $id = null;

  #[ORM\Column(type: Types::STRING, length: 255, nullable: false)]
  private ?string $name = null;

  #[ORM\Column(type: Types::STRING, length: 255, unique: true, nullable: false)]
  #[Assert\NotBlank]
  #[Assert\Length(min: 6, max: 100)]
  #[Assert\Email]
  private ?string $email = null;

  /**
   * @var string The hashed password
   */
  #[ORM\Column(type: Types::STRING, length: 255, nullable: false)]
  #[Assert\NotBlank(groups: ['password'])]
  #[Assert\Length(min: 8)]
  #[Assert\Regex(pattern: "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", message: "Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character.", groups: ['password'])]
  private ?string $password = null;

  #[ORM\Column(length: 510)]
  private ?string $apiKey = null;
  private ?string $plainPassword = null;

  /**
   * @var string[]
   */
  #[ORM\Column(type: Types::JSON)]
  private array $roles = [];

  /**
   * @var Collection<int, Customer>
   */
  #[ORM\OneToMany(targetEntity: Customer::class, mappedBy: 'user', orphanRemoval: true)]
  private Collection $customers;

  #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: false)]
  private ?\DateTimeImmutable $createdAt = null;

  #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
  private ?\DateTime $updatedAt = null;

  public function __construct()
  {
    $this->customers = new ArrayCollection();
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function setId(int $id): static
  {
    $this->id = $id;

    return $this;
  }

  public function getName(): ?string
  {
    return $this->name;
  }

  public function setName(string $name): static
  {
    $this->name = $name;

    return $this;
  }

  public function getEmail(): ?string
  {
    return $this->email;
  }

  public function setEmail(string $email): static
  {
    $this->email = $email;

    return $this;
  }

  public function getApiKey(): ?string
  {
    return $this->apiKey;
  }

  public function setApiKey(string $apiKey): static
  {
    $this->apiKey = $apiKey;

    return $this;
  }

  /**
   * @return Collection<int, Customer>
   */
  public function getCustomers(): Collection
  {
    return $this->customers;
  }

  public function addCustomer(Customer $customer): static
  {
    if (!$this->customers->contains($customer)) {
      $this->customers->add($customer);
      $customer->setuser($this);
    }

    return $this;
  }

  public function removeCustomer(Customer $customer): static
  {
    if ($this->customers->removeElement($customer)) {
      // set the owning side to null (unless already changed)
      if ($customer->getuser() === $this) {
        $customer->setuser(null);
      }
    }

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

  /**
   * A visual identifier that represents this user.
   *
   * @see UserInterface
   */
  public function getUserIdentifier(): string
  {
    return (string) $this->email;
  }

  /**
   * @see UserInterface
   *
   * @return list<string>
   */
  public function getRoles(): array
  {
    $roles = $this->roles;
    // guarantee every user at least has ROLE_USER
    $roles[] = 'ROLE_USER';

    return array_unique($roles);
  }

  /**
   * @param list<string> $roles
   */
  public function setRoles(array $roles): static
  {
    $this->roles = $roles;

    return $this;
  }

  /**
   * @see PasswordAuthenticatedUserInterface
   */
  public function getPassword(): string
  {
    return $this->password;
  }

  public function setPassword(string $password): static
  {
    $this->password = $password;

    return $this;
  }

  /**
   * @see UserInterface
   */
  public function eraseCredentials(): void
  {
    // If you store any temporary, sensitive data on the user, clear it here
    $this->plainPassword = null;
  }

  public function getPlainPassword(): ?string
  {
    return $this->plainPassword;
  }

  public function setPlainPassword(string $plainPassword): static
  {
    $this->plainPassword = $plainPassword;

    return $this;
  }
}

<?php

namespace App\Entity;

use App\Behavior\EntityInterface;
use App\Behavior\EntityTrait;
use App\Behavior\TimestampableTrait;
use App\Behavior\TimestampableTraitInterface;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Webmozart\Assert\Assert as AssertValidation;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields={"email", "username"})
 *
 * @Hateoas\Relation(
 *      "users",
 *      href = @Hateoas\Route(
 *          "api_users_list",
 *           absolute = true
 *      )
 * )
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "api_users_show",
 *           parameters = { "uuid" = "expr(object.getUuid())" },
 *           absolute = true
 *      )
 * )
 * @Hateoas\Relation(
 *      "modify",
 *      href = @Hateoas\Route(
 *          "api_users_edit",
 *          parameters = { "uuid" = "expr(object.getUuid())" },
 *          absolute = true
 *      )
 * )
 * @Hateoas\Relation(
 *      "delete",
 *      href = @Hateoas\Route(
 *          "api_users_delete",
 *          parameters = { "uuid" = "expr(object.getUuid())" },
 *          absolute = true
 *      )
 * )
 */
class User implements EntityInterface, UserInterface, TimestampableTraitInterface
{
    use EntityTrait;
    use TimestampableTrait;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(min="3")
     * @Assert\Regex(
     *     pattern="/^[a-z_]+$/",
     *     message="The username must contain only lowercase latin characters and underscores."
     * )
     * @Serializer\Groups({"user:read", "user:write"})
     */
    private string $username;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email(message="The email should look like a real email.")
     * @Serializer\Groups({"user:read", "user:write"})
     */
    private string $email;

    /**
     * @ORM\Column(type="json", nullable=false)
     * @Serializer\Groups("user:read")
     */
    private iterable $roles = ['ROLE_API'];

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Serializer\Groups({"user:password"})
     */
    private string $password;

    public static function create(): self
    {
        return new self();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return $this->email;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->ensureLength($password);

        $this->password = $password;

        return $this;
    }

    private function ensureLength(string $password): void
    {
        AssertValidation::minLength($password, 6, 'The password must be at least 6 characters long.');
    }

    /**
     * @see UserInterface
     */
    public function getSalt(): string
    {
        return uniqid('', true);
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
    }

    public function getRealUsername(): string
    {
        return $this->username;
    }
}

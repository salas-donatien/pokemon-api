<?php

namespace App\Entity;

use App\Behavior\EntityInterface;
use App\Behavior\EntityTrait;
use App\Behavior\TimestampableTrait;
use App\Behavior\TimestampableTraitInterface;
use App\Repository\PokemonRepository;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\VirtualProperty;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PokemonRepository::class)
 * @UniqueEntity(fields={"name"})
 * @ORM\HasLifecycleCallbacks()
 *
 * @Hateoas\Relation(
 *      "pokemons",
 *      href = @Hateoas\Route(
 *          "api_pokemons_list",
 *           absolute = true
 *      )
 * )
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "api_pokemons_show",
 *           parameters = { "uuid" = "expr(object.getUuid())" },
 *           absolute = true
 *      )
 * )
 * @Hateoas\Relation(
 *      "modify",
 *      href = @Hateoas\Route(
 *          "api_pokemons_edit",
 *          parameters = { "uuid" = "expr(object.getUuid())" },
 *          absolute = true
 *      )
 * )
 * @Hateoas\Relation(
 *      "delete",
 *      href = @Hateoas\Route(
 *          "api_pokemons_delete",
 *          parameters = { "uuid" = "expr(object.getUuid())" },
 *          absolute = true
 *      )
 * )
 * @Hateoas\Relation(
 *     "main_type",
 *     embedded = "expr(object.getMainType())"
 * )
 * @Hateoas\Relation(
 *     "secondary_type",
 *     embedded = "expr(object.getSecondaryType())",
 *     exclusion = @Hateoas\Exclusion(excludeIf = "expr(object.getSecondaryType() === null)")
 * )
 */
class Pokemon implements EntityInterface, TimestampableTraitInterface
{
    use EntityTrait;
    use TimestampableTrait;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     * @Serializer\Groups({"pokemon:read", "pokemon:write"})
     */
    private string $name;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Serializer\Groups({"pokemon:read", "pokemon:write"})
     */
    private int $hitPoints;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Serializer\Groups({"pokemon:read", "pokemon:write"})
     */
    private int $attack;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Serializer\Groups({"pokemon:read", "pokemon:write"})
     */
    private int $defense;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Serializer\Groups({"pokemon:read", "pokemon:write"})
     */
    private int $speedAttack;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Serializer\Groups({"pokemon:read", "pokemon:write"})
     */
    private int $speedDefense;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Serializer\Groups({"pokemon:read", "pokemon:write"})
     */
    private int $speed;

    /**
     * @ORM\Column(type="boolean")
     * @Serializer\Groups({"pokemon:read", "pokemon:write"})
     */
    private bool $legendary = false;

    /**
     * @ORM\ManyToOne(targetEntity=PokemonType::class, cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\Groups({"pokemon:write"})
     */
    private PokemonType $mainType;

    /**
     * @ORM\ManyToOne(targetEntity=PokemonType::class, cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     * @Serializer\Groups({"pokemon:write"})
     */
    private ?PokemonType $secondaryType;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Serializer\Groups({"pokemon:read", "pokemon:write"})
     */
    private int $generation;

    public static function create(): self
    {
        return new self();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getHitPoints(): ?int
    {
        return $this->hitPoints;
    }

    public function setHitPoints(int $hitPoints): self
    {
        $this->hitPoints = $hitPoints;

        return $this;
    }

    public function getAttack(): ?int
    {
        return $this->attack;
    }

    public function setAttack(int $attack): self
    {
        $this->attack = $attack;

        return $this;
    }

    public function getDefense(): ?int
    {
        return $this->defense;
    }

    public function setDefense(int $defense): self
    {
        $this->defense = $defense;

        return $this;
    }

    public function getSpeedAttack(): ?int
    {
        return $this->speedAttack;
    }

    public function setSpeedAttack(int $speedAttack): self
    {
        $this->speedAttack = $speedAttack;

        return $this;
    }

    public function getSpeedDefense(): ?int
    {
        return $this->speedDefense;
    }

    public function setSpeedDefense(int $speedDefense): self
    {
        $this->speedDefense = $speedDefense;

        return $this;
    }

    public function getSpeed(): ?int
    {
        return $this->speed;
    }

    public function setSpeed(int $speed): self
    {
        $this->speed = $speed;

        return $this;
    }

    public function getLegendary(): ?bool
    {
        return $this->legendary;
    }

    public function setLegendary(bool $legendary): self
    {
        $this->legendary = $legendary;

        return $this;
    }

    public function getMainType(): PokemonType
    {
        return $this->mainType;
    }

    public function setMainType(PokemonType $mainType): self
    {
        $this->mainType = $mainType;

        return $this;
    }

    public function getSecondaryType(): ?PokemonType
    {
        return $this->secondaryType;
    }

    public function setSecondaryType(?PokemonType $secondaryType): self
    {
        $this->secondaryType = $secondaryType;

        return $this;
    }

    public function getGeneration(): int
    {
        return $this->generation;
    }

    public function setGeneration(int $generation): self
    {
        $this->generation = $generation;

        return $this;
    }

    /**
     * @VirtualProperty()
     * @SerializedName("total")
     */
    public function getTotal(): int
    {
        return $this->hitPoints + $this->attack + $this->defense +
            $this->speedAttack + $this->speedDefense + $this->speed;
    }
}

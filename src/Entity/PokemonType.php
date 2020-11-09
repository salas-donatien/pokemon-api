<?php

namespace App\Entity;

use App\Behavior\EntityInterface;
use App\Behavior\EntityTrait;
use App\Behavior\TimestampableTrait;
use App\Behavior\TimestampableTraitInterface;
use App\Repository\PokemonTypeRepository;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PokemonTypeRepository::class)
 * @UniqueEntity(fields={"type"})
 * @ORM\HasLifecycleCallbacks()
 *
 * @Hateoas\Relation(
 *      "pokemon_types",
 *      href = @Hateoas\Route(
 *          "api_pokemon_types_list",
 *           absolute = true
 *      )
 * )
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "api_pokemon_types_show",
 *           parameters = { "uuid" = "expr(object.getUuid())" },
 *           absolute = true
 *      )
 * )
 * @Hateoas\Relation(
 *      "modify",
 *      href = @Hateoas\Route(
 *          "api_pokemon_types_edit",
 *          parameters = { "uuid" = "expr(object.getUuid())" },
 *          absolute = true
 *      )
 * )
 * @Hateoas\Relation(
 *      "delete",
 *      href = @Hateoas\Route(
 *          "api_pokemon_types_delete",
 *          parameters = { "uuid" = "expr(object.getUuid())" },
 *          absolute = true
 *      )
 * )
 */
class PokemonType implements EntityInterface, TimestampableTraitInterface
{
    use EntityTrait;
    use TimestampableTrait;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     * @Serializer\Groups({"type:read", "type:write", "pokemon:write", "pokemon:read"})
     */
    private string $type;

    public static function create(): self
    {
        return new self();
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
}

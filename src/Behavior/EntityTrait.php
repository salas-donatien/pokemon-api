<?php

namespace App\Behavior;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

trait EntityTrait
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @Serializer\Exclude()
     */
    protected ?int $id = null;

    /**
     * @ORM\Column(type="uuid", unique=true)
     * @Assert\Uuid()
     * @Serializer\Expose()
     */
    protected string $uuid;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(UuidInterface $uuid): EntityInterface
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * @ORM\PrePersist()
     */
    public function setUuidValue(): EntityInterface
    {
        $this->setUuid(Uuid::uuid4());

        return $this;
    }
}

<?php

namespace App\Behavior;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait TimestampableTrait
{
    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @Assert\Type("datetime")
     */
    protected DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @Assert\Type("datetime")
     */
    protected ?DateTimeInterface $updatedAt;

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): TimestampableTraitInterface
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeInterface $updatedAt): TimestampableTraitInterface
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @ORM\PrePersist()
     */
    public function setCreatedAtValue(): TimestampableTraitInterface
    {
        return $this->setCreatedAt(new DateTime());
    }

    /**
     * @ORM\PreUpdate()
     * @ORM\PrePersist()
     */
    public function setUpdatedAtValue(): TimestampableTraitInterface
    {
        return $this->setUpdatedAt(new DateTime());
    }
}

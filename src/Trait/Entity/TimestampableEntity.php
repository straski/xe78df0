<?php

namespace App\Trait\Entity;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\HasLifecycleCallbacks]
trait TimestampableEntity
{
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    protected DateTime $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    protected ?DateTime $updatedAt;

    #[ORM\PrePersist]
    protected function setCreatedAtValue(): void
    {
        $this->createdAt = new DateTime();
    }

    #[ORM\PreUpdate]
    protected function setUpdatedAtValue(): void
    {
        $this->updatedAt = new DateTime();
    }

    public function setCreatedAt(DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setUpdatedAt(?DateTime $updatedAt = null): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }
}

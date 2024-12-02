<?php

namespace App\Trait\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

trait IdentifiableEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    protected ?uuid $id = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }
}

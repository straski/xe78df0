<?php

namespace App\Entity;

use App\Trait\Entity\TimestampableEntity;
use App\Trait\Entity\IdentifiableEntity;
use App\{Config\ParseState,
    Config\ParseStatus,
    Controller\AbstractApiController,
    Repository\DocumentRepository
};
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: DocumentRepository::class)]
#[ORM\Table(name: "documents")]
class Document implements EntityInterface
{
    use IdentifiableEntity;
    use TimestampableEntity;

    #[ORM\Column(length: 255)]
    #[Groups([AbstractApiController::SERIALIZER_GROUP_NAME])]
    private ?string $name;

    #[ORM\ManyToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?File $file;

    /**
     * @param string|null $name
     * @param File|null $file
     */
    public function __construct(?File $file, ?string $name)
    {
        $this->file = $file;
        $this->name = $name;
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

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): static
    {
        $this->file = $file;

        return $this;
    }

    #[SerializedName("id")]
    #[Groups([AbstractApiController::SERIALIZER_GROUP_NAME])]
    public function getFileId(): string
    {
        return $this->file->getId();
    }

    #[SerializedName("parseStatus")]
    #[Groups([AbstractApiController::SERIALIZER_GROUP_NAME])]
    public function getParseStatus(): ?ParseStatus
    {
        return $this->file->getParseStatus();
    }

    #[SerializedName("parseState")]
    #[Groups([AbstractApiController::SERIALIZER_GROUP_NAME])]
    public function getParseState(): ?ParseState
    {
        return $this->file->getParseState();
    }

    #[SerializedName("uploadedAt")]
    #[Groups([AbstractApiController::SERIALIZER_GROUP_NAME])]
    public function getUploadedAt(): ?\DateTime
    {
        return $this->getCreatedAt();
    }
}

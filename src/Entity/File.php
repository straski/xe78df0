<?php

namespace App\Entity;

use App\Trait\Entity\TimestampableEntity;
use App\Trait\Entity\IdentifiableEntity;
use App\{Config\ParseState,
    Config\ParseStatus,
    Controller\AbstractApiController,
    Repository\FileRepository
};
use Doctrine\DBAL\Types\Types;
use Doctrine\{ORM\Mapping as ORM};
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FileRepository::class)]
#[ORM\Table(name: 'files')]
class File implements EntityInterface
{
    use IdentifiableEntity;
    use TimestampableEntity;

    #[Assert\EqualTo('application/pdf')]
    #[ORM\Column(length: 255, nullable: false)]
    private ?string $type;

    #[Assert\Unique]
    #[ORM\Column(length: 255, unique: true, nullable: false)]
    private ?string $sha1;

    #[ORM\Column(type: Types::BLOB)]
    private $content;

    #[Assert\Json]
    #[ORM\Column(nullable: true)]
    private ?array $parseResult = null;

    #[ORM\Column(enumType: ParseStatus::class)]
    #[Groups([AbstractApiController::SERIALIZER_GROUP_NAME])]
    private ?ParseStatus $parseStatus;

    #[ORM\Column(enumType: ParseState::class)]
    #[Groups([AbstractApiController::SERIALIZER_GROUP_NAME])]
    private ?ParseState $parseState = null;

    /**
     * @param string|null $type
     * @param null $content
     */
    public function __construct(string $type, $content)
    {
        $this->type = $type;
        $this->content = $content;
        $this->parseStatus = ParseStatus::None;
        $this->parseState = ParseState::Queued;
    }

    public function getSha1(): string
    {
        return $this->sha1;
    }

    public function setSha1(string $sha1): static
    {
        $this->sha1 = $sha1;

        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getParseResult(): ?array
    {
        return $this->parseResult;
    }

    public function setParseResult(?array $parseResult): static
    {
        $this->parseResult = $parseResult;

        return $this;
    }

    public function getParseStatus(): ?ParseStatus
    {
        return $this->parseStatus;
    }

    public function setParseStatus(ParseStatus $parseStatus): static
    {
        $this->parseStatus = $parseStatus;

        return $this;
    }

    public function getParseState(): ?ParseState
    {
        return $this->parseState;
    }

    public function setParseState(?ParseState $parseState): static
    {
        $this->parseState = $parseState;
        return $this;
    }

    public function shouldReQueue(): bool
    {
        return (
            $this->getParseState() === ParseState::Cancelled &&
            $this->getParseStatus() === ParseStatus::None
        );
    }
}

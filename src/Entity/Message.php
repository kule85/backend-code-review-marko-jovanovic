<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MessageRepository::class)]

class Message
{
    public const STATUS_SENT = 'sent';
    public const STATUS_READ = 'read';
    public const RESPONSE_MESSAGE_SENT = 'The message has been sent!';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::GUID)]
    #[Groups(['message:summary'])]
    private ?string $uuid = null;

    #[ORM\Column(length: 255)]
    #[Groups(['message:summary'])]
    private ?string $text = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['message:summary'])]
    private ?string $status = null;

    #[ORM\Column(type: 'datetime')]
    private DateTime $createdAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public static function compose(string $text, string $status = self::STATUS_SENT): self
    {
        $message = new self();
        $message->setUuid(Uuid::v6()->toRfc4122());
        $message->setText($text);
        $message->setStatus($status);
        $message->setCreatedAt(new \DateTime());

        return $message;
    }
}

<?php

use App\Entity\Message;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class MessageTest extends TestCase
{
    public function testCreateMethodSetsPropertiesCorrectly(): void
    {
        $text = 'Test message';
        $message = Message::compose($text);

        $this->assertInstanceOf(Message::class, $message);
        $this->assertSame($text, $message->getText());
        $this->assertSame(Message::STATUS_SENT, $message->getStatus());
        $this->assertNotNull($message->getUuid(), 'UUID should not be null');
        $this->assertTrue(Uuid::isValid($message->getUuid()), 'UUID should be valid');
        $this->assertInstanceOf(DateTimeInterface::class, $message->getCreatedAt());
    }

    public function testCreateMethodAllowsCustomStatus(): void
    {
        $text = 'Read message';
        $status = Message::STATUS_READ;
        $message = Message::compose($text, $status);

        $this->assertSame($text, $message->getText());
        $this->assertSame($status, $message->getStatus());
    }
}

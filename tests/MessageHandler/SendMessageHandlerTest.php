<?php

namespace App\Tests\Message;

use App\Entity\Message;
use App\Message\SendMessage;
use App\Message\SendMessageHandler;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class SendMessageHandlerTest extends TestCase
{
    public function testHandlerPersistsValidMessage(): void
    {
        /** @var Message|null */
        $persistedEntity = null;

        /** @var \PHPUnit\Framework\MockObject\MockObject&EntityManagerInterface $mockEntityManager */
        $mockEntityManager = $this->createMock(EntityManagerInterface::class);

        $mockEntityManager->expects($this->once())
            ->method('persist')
            ->willReturnCallback(function ($entity) use (&$persistedEntity) {
                $persistedEntity = $entity;
            });

        $mockEntityManager->expects($this->once())
            ->method('flush');

        $sendMessageHandler = new SendMessageHandler($mockEntityManager);
        $messageText = 'Sample message content';
        $command = new SendMessage($messageText);

        $sendMessageHandler($command);

        $this->assertInstanceOf(Message::class, $persistedEntity);
        $this->assertSame($messageText, $persistedEntity->getText());
        $this->assertSame(Message::STATUS_SENT, $persistedEntity->getStatus());
    }
}

<?php

declare(strict_types=1);

namespace App\Message;

use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SendMessageHandler
{
    public function __construct(private EntityManagerInterface $manager)
    {
    }

    public function __invoke(SendMessage $sendMessage): void
    {
        $message = Message::compose($sendMessage->text);

        $this->manager->persist($message);
        $this->manager->flush();
    }
}

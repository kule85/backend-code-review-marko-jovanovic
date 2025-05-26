<?php

use App\Entity\Message;
use App\Dto\MessageFilter;
use App\DataFixtures\AppFixtures;
use App\Repository\MessageRepository;
use App\Service\Message\MessageService;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\ORM\EntityManagerInterface;
use App\Tests\Traits\MessageAssertionsTrait;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MessageServiceTest extends KernelTestCase
{
    use MessageAssertionsTrait;

    private MessageService $service;
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = self::getContainer();

        $em = $container->get(EntityManagerInterface::class);
        if (!$em instanceof EntityManagerInterface) {
            throw new \LogicException('Expected EntityManagerInterface from container.');
        }
        $this->em = $em;

        $repository = $this->em->getRepository(Message::class);
        if (!$repository instanceof MessageRepository) {
            throw new \LogicException('Expected MessageRepository instance.');
        }

        $this->service = new MessageService($repository);

        $loader = new Loader();
        $loader->addFixture(new AppFixtures());

        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();
        $executor->execute($loader->getFixtures());
    }

    public function testListMessagesWithSentStatus(): void
    {
        $messageDto = new MessageFilter(status: Message::STATUS_SENT);
        $response = $this->service->listMessages($messageDto);

        $this->assertCount(5, $response);
        $this->assertAllMessagesHaveStatus($response, Message::STATUS_SENT);
    }

    public function testListMessagesWithReadStatus(): void
    {
        $messageDto = new MessageFilter(status: Message::STATUS_READ);
        $response = $this->service->listMessages($messageDto);

        $this->assertCount(5, $response);
        $this->assertAllMessagesHaveStatus($response, Message::STATUS_READ);
    }

    public function testListMessagesWithoutStatus(): void
    {
        $messageDto = new MessageFilter(status: null);
        $response = $this->service->listMessages($messageDto);

        $sentMessages = array_filter($response, fn ($message) => $message->getStatus() === Message::STATUS_SENT);
        $readMessages = array_filter($response, fn ($message) => $message->getStatus() === Message::STATUS_READ);

        $this->assertCount(10, $response);
        $this->assertCount(5, $sentMessages);
        $this->assertCount(5, $readMessages);

        $this->assertAllMessagesHaveStatus($sentMessages, Message::STATUS_SENT);
        $this->assertAllMessagesHaveStatus($readMessages, Message::STATUS_READ);
    }
}

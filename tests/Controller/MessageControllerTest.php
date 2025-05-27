<?php

declare(strict_types=1);

namespace Controller;

use App\Entity\Message;
use App\Message\SendMessage;
use App\Tests\Traits\MessageAssertionsTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Messenger\Test\InteractsWithMessenger;

class MessageControllerTest extends WebTestCase
{
    use InteractsWithMessenger;
    use MessageAssertionsTrait;

    public function testListReturnsMessagesFilteredByStatus(): void
    {
        $client = static::createClient();
        $client->request('GET', '/message', ['status' => Message::STATUS_SENT]);

        $this->assertResponseIsSuccessful();

        $responseContent = $client->getResponse()->getContent();
        $this->assertNotFalse($responseContent, 'Response content should not be false');

        $response = json_decode($responseContent, true);
        $this->assertIsArray($response, 'Decoded response must be an array');

        $this->assertAllResponseMessagesHaveStatus($response, Message::STATUS_SENT);
    }

    public function testListReturnsMessagesWhenStatusIsNotProvided(): void
    {
        $client = static::createClient();
        $client->request('GET', '/message');

        $this->assertResponseIsSuccessful();

        $responseContent = $client->getResponse()->getContent();
        $this->assertNotFalse($responseContent, 'Response content should not be false');

        $response = json_decode($responseContent, true);
        $this->assertIsArray($response, 'Decoded response must be an array');

        $sentMessages = array_filter($response, fn ($message) => isset($message['status']) && $message['status'] === Message::STATUS_SENT);
        $readMessages = array_filter($response, fn ($message) => isset($message['status']) && $message['status'] === Message::STATUS_READ);

        $this->assertCount(10, $response);
        $this->assertCount(5, $sentMessages);
        $this->assertCount(5, $readMessages);
        $this->assertAllResponseMessagesHaveStatus($sentMessages, Message::STATUS_SENT);
        $this->assertAllResponseMessagesHaveStatus($readMessages, Message::STATUS_READ);
    }

    public function testSendDispatchesMessageAndReturns201(): void
    {
        $client = static::createClient();
        $jsonContent = json_encode(['text' => 'Test message']);

        $client->request(
            'POST',
            '/message/send',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $jsonContent === false ? null : $jsonContent
        );

        $this->assertResponseStatusCodeSame(201);

        $responseContent = $client->getResponse()->getContent();
        $this->assertNotFalse($responseContent, 'Response content should not be false');

        $this->assertJson($responseContent);

        $response = json_decode($responseContent, true);
        $this->assertIsArray($response, 'Decoded response must be an array');

        $this->assertArrayHasKey('message', $response);
        $this->assertSame(Message::RESPONSE_MESSAGE_SENT, $response['message']);

        $this->transport('async')
            ->queue()
            ->assertContains(SendMessage::class, 1);
    }


    public function testSendFailsValidationWithoutText(): void
    {
        $client = static::createClient();
        $jsonContent = json_encode([]);

        $client->request(
            'POST',
            '/message/send',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $jsonContent === false ? null : $jsonContent
        );

        $this->assertResponseStatusCodeSame(400);

        $responseContent = $client->getResponse()->getContent();
        $this->assertNotFalse($responseContent, 'Response content should not be false');

        $response = json_decode($responseContent, true);
        $this->assertIsArray($response, 'Decoded response must be an array');

        $this->assertArrayHasKey('errors', $response);
        $this->assertArrayHasKey('text', $response['errors']);
    }


    public function testSendFailsValidationWithNonStringText(): void
    {
        $client = static::createClient();
        $jsonContent = json_encode(['text' => 123]);

        $client->request(
            'POST',
            '/message/send',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $jsonContent === false ? null : $jsonContent
        );

        $this->assertResponseStatusCodeSame(400);

        $responseContent = $client->getResponse()->getContent();
        $this->assertNotFalse($responseContent, 'Response content should not be false');

        $response = json_decode($responseContent, true);
        $this->assertIsArray($response, 'Decoded response must be an array');

        $this->assertArrayHasKey('errors', $response);
        $this->assertArrayHasKey('text', $response['errors']);
    }
}

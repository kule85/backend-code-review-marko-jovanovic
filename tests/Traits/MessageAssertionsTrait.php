<?php

namespace App\Tests\Traits;

trait MessageAssertionsTrait
{
    /**
     * @param \App\Entity\Message[] $messages
     */
    protected function assertAllMessagesHaveStatus(array $messages, string $expectedStatus): void
    {
        foreach ($messages as $message) {
            $this->assertSame(
                $expectedStatus,
                $message->getStatus(),
                sprintf('Expected message status to be "%s", got "%s"', $expectedStatus, $message->getStatus())
            );
        }
    }

    /**
     * @param array<array{status: string}> $responseData
     */
    protected function assertAllResponseMessagesHaveStatus(array $responseData, string $expectedStatus): void
    {
        foreach ($responseData as $item) {
            $this->assertIsArray($item, 'Each item in response should be an array.');
            $this->assertArrayHasKey('status', $item, 'Each item should contain a "status" key.');

            $this->assertSame(
                $expectedStatus,
                $item['status'],
                sprintf('Expected message status to be "%s", got "%s"', $expectedStatus, $item['status'])
            );
        }
    }
}

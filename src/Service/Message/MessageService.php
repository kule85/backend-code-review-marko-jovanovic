<?php

namespace App\Service\Message;

use App\Dto\MessageFilter;
use App\Repository\MessageRepository;
use App\Entity\Message;

class MessageService
{
    public function __construct(private readonly MessageRepository $repository)
    {
    }

    /**
     * @return Message[]
     */
    public function listMessages(MessageFilter $dto): array
    {
        return $this->repository->findByOptionalStatus($dto->status);
    }
}

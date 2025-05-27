<?php

namespace App\Dto;

use App\Entity\Message;
use Symfony\Component\Validator\Constraints as Assert;

class MessageFilter
{
    #[Assert\Choice(
        choices: [
            Message::STATUS_SENT,
            Message::STATUS_READ,
        ],
        message: 'The status must be one of: {{ choices }}.'
    )]
    public ?string $status = null;

    public function __construct(?string $status = null)
    {
        $this->status = $status;
    }
}

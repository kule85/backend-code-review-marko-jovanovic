<?php

declare(strict_types=1);

namespace App\Message;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * This class is used both as a message (for the message bus) and as a DTO.
 * Because of that, we decided to add validation constraints directly to the properties,
 * so the message can be validated during instantiation or processing.
 */
class SendMessage
{
    #[Assert\NotBlank(message: 'Text is required')]
    #[Assert\Type('string', message: 'Text must be a string')]
    public string $text;

    public function __construct(string $text)
    {
        $this->text = $text;
    }
}

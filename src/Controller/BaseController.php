<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class BaseController extends AbstractController
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        protected readonly SerializerInterface $serializer,
    ) {
    }

    /**
     * @return array<string, string[]>
     */
    protected function validate(object $formData): array
    {
        /** @var array<string, string[]> $messages */
        $messages = [];
        $errors = $this->validator->validate($formData);

        if ($errors->count() > 0) {
            foreach ($errors as $violation) {
                $messages[(string) $violation->getPropertyPath()][] = (string) $violation->getMessage();
            }
        }

        return $messages;
    }
}

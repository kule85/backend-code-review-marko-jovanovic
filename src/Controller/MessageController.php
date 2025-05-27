<?php

declare(strict_types=1);

namespace App\Controller;

use App\Message\SendMessage;
use App\Dto\MessageFilter;
use App\Entity\Message;
use App\Service\Message\MessageService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Messenger\MessageBusInterface;

#[Route('/message')]
class MessageController extends BaseController
{
    #[Route('', methods: ['GET'])]
    public function list(Request $request, MessageService $messageService): Response
    {
        $statusRaw = $request->query->get('status');
        $status = is_string($statusRaw) || $statusRaw === null ? $statusRaw : null;

        $messageFilter = new MessageFilter($status);
        $errors = $this->validate($messageFilter);

        if (count($errors) > 0) {
            return $this->json(['errors' => $errors], 400);
        }

        $response = $messageService->listMessages($messageFilter);

        return $this->json($response, 200, [], ['groups' => ['message:summary']]);
    }

    #[Route('/send', methods: ['POST'])]
    public function send(Request $request, MessageBusInterface $bus): Response
    {
        $requestData = json_decode($request->getContent(), true);

        if (!is_array($requestData)) {
            return $this->json(['errors' => ['Invalid JSON']], 400);
        }

        if (!isset($requestData['text']) || !is_string($requestData['text'])) {
            return $this->json(['errors' => ['text' => 'This value should be a non-empty string.']], 400);
        }

        $newMessage = new SendMessage($requestData['text']);
        $errors = $this->validate($newMessage);

        if (count($errors) > 0) {
            return $this->json(['errors' => $errors], 400);
        }

        $bus->dispatch($newMessage);

        return $this->json(['message' => Message::RESPONSE_MESSAGE_SENT], 201);
    }
}

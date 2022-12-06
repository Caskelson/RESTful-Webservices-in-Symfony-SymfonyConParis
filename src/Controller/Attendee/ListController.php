<?php

declare(strict_types=1);

namespace App\Controller\Attendee;

use App\Repository\AttendeeRepository;
use App\Serializer\AttendeeNormalizer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/attendees', name: 'list_attendee', methods: ['GET'])]
final class ListController
{
    public function __construct(
        private AttendeeRepository $attendeeRepository,
        private AttendeeNormalizer $normalizer
    ) {
    }

    public function __invoke(): Response
    {
        $allAttendees = $this->attendeeRepository->findAll();

        $allAttendeesAsArray = $this->normalizer->normalize($allAttendees);

        return new Response(json_encode($allAttendeesAsArray), Response::HTTP_OK, [
            'Content-Type' => 'application/json',
        ]);
    }
}

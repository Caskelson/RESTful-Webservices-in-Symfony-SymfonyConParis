<?php

declare(strict_types=1);

namespace App\Controller\Attendee;

use App\Entity\Attendee;
use App\Serializer\AttendeeNormalizer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/attendees/{identifier}', name: 'read_attendee', methods: ['GET'])]
final class ReadController
{
    public function __construct(
        private AttendeeNormalizer $normalizer
    ) {
    }

    public function __invoke(Attendee $attendee): Response
    {
        return new Response(json_encode($this->normalizer->normalize($attendee)), Response::HTTP_OK, [
            'Content-Type' => 'application/json',
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Entity\Attendee;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

final class AttendeeNormalizer implements ContextAwareNormalizerInterface
{
    private Serializer $serializer;

    public function __construct(
    ) {
        $this->serializer = new Serializer([new ObjectNormalizer()]);
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Attendee;
    }

    /**
     * @param Attendee|Attendee[] $object
     *
     * @return array|string
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => fn ($object, $format, $context) => $object->getFirstname().' '.$object->getLastname(),
        ];

        if (is_array($object)) {
            $allNormalizedObjects = [];
            foreach ($object as $attendee) {
                $allNormalizedObjects[] = $this->serializer->normalize($attendee, $format, $defaultContext);
            }
            return $allNormalizedObjects;
        }

        return $this->serializer->normalize($object, $format, $defaultContext);
    }
}

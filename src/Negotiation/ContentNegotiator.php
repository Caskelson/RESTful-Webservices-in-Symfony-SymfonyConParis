<?php

declare(strict_types=1);

use App\Negotiation\MimeTypes;
use App\Negotiation\RequestFormats;
use Negotiation\Negotiator;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

final class ContentNegotiator
{
    private const SUPPORTED_MIME_TYPES = [
        MimeTypes::JSON => 'application/json',
        MimeTypes::JSON_HAL => 'application/hal+json',
        MimeTypes::XML => 'text/xml'
    ];

    private Negotiator $negotiator;

    public function __construct(
        private RequestStack $requestStack
    ) {
        $this->negotiator = new Negotiator();
    }

    private function getNegotiatedMimeType(array $contentTypes): ?string
    {
        //default is application/json
        if (empty($contentTypes) ||
            (1 === count($contentTypes) && '*/*' === $contentTypes[0])
        ) {
            return MimeTypes::JSON;
        }

        $acceptableContentTypesAsString = implode(',', $contentTypes);
        $acceptHeader = $this->negotiator->getBest($acceptableContentTypesAsString, self::SUPPORTED_MIME_TYPES);

        return $acceptHeader?->getType();
    }

    public function getNegotiatedRequestFormat(): string
    {
        $acceptableContentTypes = $this->requestStack->getCurrentRequest()->getAcceptableContentTypes();

        $mimeType = $this->getNegotiatedMimeType($acceptableContentTypes);

        if (!$mimeType || !in_array($mimeType, self::SUPPORTED_MIME_TYPES, true)) {
            throw $this->getNotAcceptableHttpException($acceptableContentTypes, self::SUPPORTED_MIME_TYPES);
        }

        return match ($mimeType) {
            MimeTypes::JSON_HAL => RequestFormats::JSON_HAL,
            MimeTypes::XML => RequestFormats::XML,
            default => RequestFormats::JSON,
        };
    }

    private function getNotAcceptableHttpException(array $accepts): NotAcceptableHttpException
    {
        return new NotAcceptableHttpException(sprintf(
            'Requested format "%s" is not supported. Supported MIME types are "%s".',
            implode('", "', $accepts),
            implode('", "', self::SUPPORTED_MIME_TYPES)
        ));
    }

    public function isNegotiatedRequestFormat(string $format): bool
    {
        return $format === $this->getNegotiatedRequestFormat();
    }
}

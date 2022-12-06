<?php

declare(strict_types=1);

use App\Negotiation\MimeTypes;
use App\Negotiation\RequestFormats;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(event: KernelEvents::REQUEST, method: 'onKernelRequest', priority: 8)]
final class RequestFormatListener
{
    private array $formats = [
        RequestFormats::JSON => MimeTypes::JSON,
        RequestFormats::JSON_HAL => MimeTypes::JSON_HAL,
        RequestFormats::XML => MimeTypes::XML,
    ];

    public function __construct(
        private ContentNegotiator $contentNegotiator
    ) {
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        $this->addRequestFormats($request, $this->formats);

        $request->setRequestFormat(
            $this->contentNegotiator->getNegotiatedRequestFormat()
        );
    }

    /**
     * Adds the supported formats to the request.
     *
     * This is necessary for {@see Request::getMimeType} to work.
     */
    private function addRequestFormats(Request $request, array $formats): void
    {
        foreach ($formats as $format => $mimeType) {
            $request->setFormat($format, $mimeType);
        }
    }
}

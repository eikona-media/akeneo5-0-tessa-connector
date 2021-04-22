<?php
/**
 * AddContentSecurityPolicyListener.php
 *
 * @author      Timo Müller <t.mueller@eikona-media.de>
 * @copyright   2020 EIKONA Media (https://eikona-media.de)
 */

namespace Eikona\Tessa\ConnectorBundle\EventListener;

use Eikona\Tessa\ConnectorBundle\Tessa;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class AddContentSecurityPolicyListener
 *
 * @package Eikona\Tessa\ConnectorBundle\EventListener
 */
class AddContentSecurityPolicyListener implements EventSubscriberInterface
{
    /** @var Tessa */
    private $tessa;

    public function __construct(Tessa $tessa)
    {
        $this->tessa = $tessa;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'addCspHeaders',
        ];
    }

    public function addCspHeaders(ResponseEvent $event): void
    {
        $url = $this->tessa->getBaseUrl();
        if (empty($url) || ($parsedUrl = parse_url($url)) === false) {
            return;
        }

        $response = $event->getResponse();

        $policy = $response->headers->get('Content-Security-Policy');
        $parsedCsp = $this->parseCsp($policy);
        if (!array_key_exists('img-src', $parsedCsp)) {
            $parsedCsp['img-src'] = [];
        }
        $parsedCsp['img-src'][] = $parsedUrl['host'];
        $policy = $this->formatCsp($parsedCsp);

        $response->headers->set('Content-Security-Policy', $policy);
        $response->headers->set('X-Content-Security-Policy', $policy);
        $response->headers->set('X-WebKit-CSP', $policy);
    }

    private function parseCsp($csp)
    {
        $parsedCspDirectives = [];
        $directives = explode(';', $csp);
        foreach ($directives as $directive) {
            $splitParts = preg_split('/\s+/', trim($directive));
            $directiveKey = $splitParts[0];
            $directiveValues = array_slice($splitParts, 1);
            $parsedCspDirectives[$directiveKey] = $directiveValues;
        }
        return $parsedCspDirectives;
    }

    private function formatCsp($parsedCsp)
    {
        $formattedCsp = '';
        foreach ($parsedCsp as $directiveKey => $directiveValues) {
            if (!empty($directiveValues)) {
                if (!empty($formattedCsp)) {
                    $formattedCsp .= ' ';
                }
                $formattedCsp .= $directiveKey . ' ' . implode(' ', $directiveValues) . ' ;';
            }
        }
        return $formattedCsp;
    }
}

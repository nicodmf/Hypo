<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hypo\LayoutBundle\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Bundle\TwigBundle\TwigEngine;

/**
 * WebDebugToolbarListener injects the Web Debug Toolbar.
 *
 * The onKernelResponse method must be connected to the kernel.response event.
 *
 * The WDT is only injected on well-formed HTML (with a proper </body> tag).
 * This means that the WDT is never included in sub-requests or ESI requests.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class HypoLayoutListener
{
    const DISABLED        = 1;
    const ENABLED         = 2;
    const ENABLED_MINIMAL = 3;

    protected $templating;
    protected $interceptRedirects;
    protected $mode;

    public function __construct(TwigEngine $templating,  $configuration, $interceptRedirects = false, $mode = self::ENABLED)
    {
        $this->templating = $templating;
		$this->configuration = $configuration;
        $this->interceptRedirects = (Boolean) $interceptRedirects;
        $this->mode = (integer) $mode;
    }

    public function isVerbose()
    {
        return self::ENABLED === $this->mode;
    }

    public function isEnabled()
    {
        return self::DISABLED !== $this->mode;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $response = $event->getResponse();
        $request = $event->getRequest();

        // do not capture redirects or modify XML HTTP Requests
        if ($request->isXmlHttpRequest()) {
            return;
        }

        if (self::DISABLED === $this->mode
            || !$response->headers->has('X-Debug-Token')
            || '3' === substr($response->getStatusCode(), 0, 1)
            ||	(
				$response->headers->has('Content-Type')
				&& false === strpos($response->headers->get('Content-Type'), 'html')
				)
            || 'html' !== $request->getRequestFormat()
        ) {
            return;
        }
		
		$paramsConfig = $this->configuration['parameters'];

		$params = array_merge(
			array('content' => $response->getContent()),
			is_array($paramsConfig)?$paramsConfig:array()
		);
        $response->setContent(
			$this->templating->render(
				$this->configuration['template'],
				$params
			)
		);
        $response->setStatusCode(200);

    }
}

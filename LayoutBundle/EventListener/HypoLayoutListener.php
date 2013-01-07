<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hypo\LayoutBundle\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Hypo\LayoutBundle\DependencyInjection\LayoutTwigExtension;

/**
 * WebDebugToolbarListener injects the Web Debug Toolbar.
 *
 * The onKernelResponse method must be connected to the kernel.response event.
 *
 * The WDT is only injected on well-formed HTML (with a proper </body> tag).
 * This means that the WDT is never included in sub-requests or ESI requests.
 *
 */
class HypoLayoutListener
{
	 const DISABLED		  = 0;
	 const ENABLED			  = 1;
	 const ENABLED_MINIMAL = 2;

	 protected $templating;
	 protected $controllerListener;
	 protected $interceptRedirects;
	 protected $mode;

	 public function __construct(
				TwigEngine $templating, 
				LayoutTwigExtension $layout,
				ControllerListener $controllerListener,
				array $configuration,
				$interceptRedirects = false,
				$mode = self::ENABLED)
	 {		 
		  $this->templating = $templating;		  
		  $this->layout = $layout;
		  $this->controllerListener = $controllerListener;
		  $this->configuration = $configuration;		  
		  $this->interceptRedirects = (Boolean) $interceptRedirects;
		  
		  $this->mode = $configuration['activated'] ?: (integer) $mode;
		  $this->templates = $configuration['templates'];
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
//				|| !$response->headers->has('X-Debug-Token')
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
		

		$controller = $request->attributes->get('_controller');

		$template = $this->getTemplateName(
					$this->controllerListener,
					$controller,
					$this->configuration['templates']);

		$params = array_merge(
			is_array($paramsConfig) ? $paramsConfig : array(),
			array('content' => $response->getContent()),
			$this->layout->variables
		);
		if($template!=null)
			$response->setContent(
				$this->templating->render(				
					$template,				
					$params
				)
			);
		$response->setStatusCode(200);
	 }
	 
	 public function getTemplateName(&$controllerListener, &$controller_method, &$templates){
		 if($controllerListener->layout)
			 return $controllerListener->layout;
		 foreach($templates as $target=>$template){
			 if($target=="default")continue;
			 if(0===strpos($controller_method, $target)){
				 return $template;
			 }
		 }
		 return $templates['default'];
	 }
}

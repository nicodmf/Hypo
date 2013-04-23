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
		

		$controller_method = $request->attributes->get('_controller');

		$template = $this->getTemplateName(
					$this->controllerListener->layout,
					$controller_method,
					$this->configuration['templates']);

		$blocs = $this->getBlocs(
					$this->controllerListener->blocs,
					$controller_method,
					$this->configuration['blocs']);

		$params = array_merge(
			is_array($paramsConfig) ? $paramsConfig : array(),
			array('content' => $response->getContent()),
			$this->layout->variables,
			$blocs
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
	 
	 public function getTemplateName($layout, &$controller_method, &$templates){
		 if($layout)
			 return $layout;
		 foreach($templates as $name=>$config){
			 if($name=="default") continue;
			 foreach($config['targets'] as $target){
				if(0===strpos($controller_method, $target)){
					return $config['template'];
				}
			 }
		 }
		 return $templates['default'];
	 }
	 
	 public function getBlocs($_blocs, &$controller_method, &$config_blocs){
		 $blocs = array();
		 $base_template = preg_replace("#::.*#", "", $controller_method);
		 $base_template = preg_replace("#\\\Controller#", ":", $base_template);
		 $base_template = preg_replace("#Controller#", "", $base_template);
		 $base_template = preg_replace("#\\\#", "", $base_template);
		 foreach ($_blocs as $key=>$value) {
			if(is_numeric($key)){
				$name = $value;				
				$template = $base_template.":".$name.".html.twig";
			}else{
				$name = $key;
				$template = $value;
			}
			$blocs[$name] = $this->templating->render($template, $blocs);
		 }
		 /*
		 foreach($config_vars as $name=>$config){
			 if($name=="default")$vars = array_merge_recursive ($config, $vars);
			 foreach($config['target'] as $target){
				if(0===strpos($controller_method, $target)){
					$vars = array_merge_recursive ($config['vars'], $vars);
				}
			 }
		 }
		 return $vars;*/
		 return $blocs;
	 }
}

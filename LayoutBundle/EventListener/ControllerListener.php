<?php

namespace Hypo\LayoutBundle\EventListener;

use Doctrine\Common\Annotations\Reader;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Hypo\LayoutBundle\Annotations\Layout;
 
class ControllerListener {
	
	const annoLayoutCName = "Hypo\LayoutBundle\Annotations\Layout";
	const annoBlocCName = "Hypo\LayoutBundle\Annotations\Bloc";

	public $layout = false;
	public $blocs = array();
	 
	private $reader;

	public function __construct(Reader $reader) {
		$this->reader = $reader;
	}
	 
	public function onCoreController(FilterControllerEvent $event) {
		$this->annotationReader($event); 
	}
	
	private function annotationReader(FilterControllerEvent $event)
	{
		if (!is_array($controller = $event->getController()))
			return;
 
		$this->layoutAnnotationReader($controller[0], $controller[1]);
		$this->blocAnnotationReader($controller[0], $controller[1]);
	}
	private function layoutAnnotationReader($class, $method)
	{
		if(!$annotation = $this->reader->getMethodAnnotation(new \ReflectionMethod($class, $method), self::annoLayoutCName))
			if(!$annotation = $this->reader->getClassAnnotation(new \ReflectionClass($class), self::annoLayoutCName))
				return;
		if(! $annotation instanceof Layout)
			throw new \Exception("Something go wrong with reader.");

		
		$this->layout = $annotation->layout;
	}
	private function blocAnnotationReader($class, $method)
	{
		if(!$annotation = $this->reader->getMethodAnnotation(new \ReflectionMethod($class, $method), self::annoBlocCName))
			if(!$annotation = $this->reader->getClassAnnotation(new \ReflectionClass($class), self::annoBlocCName))
				return;
		if(! $annotation instanceof Bloc)
			throw new \Exception("Something go wrong with reader.");
		
		$this->blocs = $annotation->blocs;
	}
}
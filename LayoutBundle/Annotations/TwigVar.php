<?php
// MyBundle/Annotation/MyAnnotation.php
namespace Hypo\LayoutBundle\Annotations;

/**
 * @Annotation
 */
class TwigVar {
	
	public $twigVar;
   
	public function __construct(array $data) {
		$this->twigVar = $data['value'];
	}	
}
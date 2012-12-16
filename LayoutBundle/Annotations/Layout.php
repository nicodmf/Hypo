<?php
// MyBundle/Annotation/MyAnnotation.php
namespace Hypo\LayoutBundle\Annotations;

/**
 * @Annotation
 */
class Layout {
	
	public $layout;
   
	public function __construct(array $data) {
		$this->layout = $data['value'];
	}	
}
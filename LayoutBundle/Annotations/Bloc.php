<?php
// MyBundle/Annotation/MyAnnotation.php
namespace Hypo\LayoutBundle\Annotations;

/**
 * @Annotation
 */
class Bloc {
	
	public $blocs;
   
	public function __construct(array $data) {
		$this->blocs = $data['value'];
	}	
}
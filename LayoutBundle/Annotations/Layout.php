<?php

namespace Hypo\LayoutBundle\Annotations;

/**
 * @Annotation
 */
class Layout {
	
	public $layout;
   
	public function __construct(array $data) {
		$this->layout = isset($data) ? $data['value'] : null;
	}	
}
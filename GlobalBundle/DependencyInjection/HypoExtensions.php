<?php

namespace Hypo\GlobalBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class HypoExtensions extends Extension
{
	 public function load(array $configs, ContainerBuilder $container)
	 {
		$config = array();
	 	foreach ($configs as $subConfig) {
		  	$config = array_merge($config, $subConfig);
	 	}
		
		$templates = array();
		/*foreach ($config['layout']['templates'] as $key=>$value){
			if($key=='default'){
				$default = $value;
				continue;
			}
			$template = $value['template'];
			foreach($value['targets'] as $target){
				$templates[$target] = $template;
			}
			$templates["default"] = $default;
		}
		
		$config['layout']['templates'] = $templates;*/
			
		$container->setParameter('hypo.layout.configuration', $config['layout']);
	}
	public function getAlias(){
		return 'hypo';
	}
}
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
		$container->setParameter('hypo.layout.configuration', $config['layout']);
    }
	public function getAlias(){
		return 'hypo';
	}
}
<?php

namespace Hypo\LayoutBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

class HypoLayoutBundle extends Bundle
{
    function __construct(){
    }
    public function build(ContainerBuilder $container)
    {
        // register the extension(s) found in DependencyInjection/ directory
        parent::build($container);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/Resources/config'));
        $loader->load('services.yml');
    }
}

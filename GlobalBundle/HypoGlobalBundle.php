<?php

namespace Hypo\GlobalBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Hypo\GlobalBundle\DependencyInjection\HypoExtensions;

class HypoGlobalBundle extends Bundle
{
    function __construct(){
    }
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->registerExtension(new HypoExtensions());
    }
}

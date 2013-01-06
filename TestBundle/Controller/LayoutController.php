<?php

namespace Hypo\TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Hypo\LayoutBundle\Annotations\Layout;

class LayoutController extends Controller
{
    /**
     * @Route("/", name="hypo_test_default_index")
     * @Template()
	 * @Layout("HypoTestBundle::layout.html.twig")
     */
    public function indexAction()
    {
        return array();
    }
}

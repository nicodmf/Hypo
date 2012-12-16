<?php

namespace Hypo\LayoutBundle\DependencyInjection;

class VariableNode extends \Twig_Node
{
	public function __construct($capture, \Twig_NodeInterface $variable, \Twig_NodeInterface $merge, \Twig_NodeInterface $source, $lineno, $tag = null)
	{
		parent::__construct(
			array('source'=>$source, 'variable'=>$variable, 'merge'=>$merge),
			array('capture' => $capture), $lineno, $tag
		);
	}
	public function compile(\Twig_Compiler $compiler)
	{
		$rand = md5(rand().time());
		//print_r($this->getNode('variable'));
		$compiler
			->addDebugInfo($this)
			->write('ob_start();')
			->subcompile($this->getNode('source'))
			->write("\$context['source_$rand'] = new Twig_Markup(ob_get_clean(), \$this->env->getCharset());\n")
			->write("\$context['variable_$rand'] = ")->subcompile($this->getNode('variable'))->raw(";\n")
			->write("\$context['merge_$rand'] = ")->subcompile($this->getNode('merge'))->raw(";\n")
			->write("echo \$this->env->getExtension('twig.extension.layout')->variable(\n")
			->write("   \$this->getContext(\$context, 'variable_$rand'),\n")
			->write("   \$this->getContext(\$context, 'source_$rand'),\n")
			->write("   \$this->getContext(\$context, 'merge_$rand') \n")
			->write(");\n");
		;
	}
}
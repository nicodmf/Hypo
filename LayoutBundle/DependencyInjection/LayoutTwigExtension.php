<?php

namespace Hypo\LayoutBundle\DependencyInjection;

use Symfony\Component\Translation\TranslatorInterface;

class Container{
	static $inc=1;
	public $order;
	public $position=null;
	public $link=null;
	public $html;
	public function __construct($position=null, $html='html') {
		$this->position = $position;
		$this->html = $html==="html";
		$this->order = static::$inc++;
	}
	static public function create($position=null, $html='html') {
		$class = get_called_class();	
		return new $class($position, $html);
	}
	public function isLink(){
		return $this->link !== null;
	}
	static function compare(Container $c1, Container $c2){
		if( $c1->position !== $c2->position )
			return $c1->position < $c2->position ? -1 : 1;
		return $c1->order < $c2->order ? -1 : 1;
	}
	public function setLink($link) {
		$this->link = $link;
		return $this;
	}
}

class CSS extends Container{
	public $rules=null;
	public $media="all";
	public function __construct($media="all", $position=null, $html='html'){
		$this->media = $media;
		parent::__construct($position, $html);
	}
	static public function create($media="all", $position=null, $html='html') {
		return new CSS($media, $position, $html);
	}
	static function compare(Container $css1, Container $css2){
		if($css1->rules === null && $css2->rules !== null) return  1;
		if($css1->rules !== null && $css2->rules === null) return -1;
		$cpMedia = strcmp($css1->media, $css2->media);
		if($cpMedia!==0)
			return $cpMedia;
		return parent::compare($css1, $css2);
	}
	public function setRules($rules) {
		$this->rules = $rules;
		return $this;
	}
}
class JS extends Container{
	public $script=null;
	public function setScript($script) {
		$this->script = $script;
		return $this;
	}
}

class LayoutTwigExtension extends \Twig_Extension {
	
	protected $css = array();
	protected $js = array();
	public $variables = array();

	public function __construct()
	{
	}
	
	public function getFilters()
	{
		return array(
			'css'  => new \Twig_Filter_Method($this, 'filter_css', array('is_safe' => array('html'))),
			'js'  => new \Twig_Filter_Method($this, 'filter_js', array('is_safe' => array('html'))),
		);
	}

	public function getFunctions()
	{
		//\Twig_NodeVisitor_SafeAnalysis::setSafe();
		return array(
			'css'  => new \Twig_Function_Method($this, 'css', array('is_safe' => array('html'))),
			'js'  => new \Twig_Function_Method($this, 'js', array('is_safe' => array('html'))),
		);
	}

	public function getTokenParsers()
	{
		return array(
			new VariableTokenParser(),
		);
	}
	
	public function variable($name, $value, $merge=false){
		$this->variables[$name] = $merge ? $this->variables[$name] : "";
		$this->variables[$name] = "$value";		
	}

	public function filter_css($css, $position=null, $media="all", $html='html')
	{
		$this->css('src', $css, $media, $position, $html);
	}
	public function filter_js($js, $position=null, $html='html')
	{ 
		$this->js('src', $js, $position, $html);
	}
	public function css($action='display', $css="", $position=null, $media="all", $html='html')
	{
		switch($action){
			case 'display' : return $this->getcss($media, $position, $html);
			case 'link': $this->css[] = CSS::create($media, $position, $html)->setLink($css); break;
			case 'src': $this->css[] = CSS::create($media, $position, $html)->setRules($css); break;
			default: call_user_func_array(array($this, "js"), array_merge(array("link"),func_get_args())); break;
		}
	}
	public function js($action='display', $js="", $position=null, $html='html')
	{
		switch($action){
			case 'display' : return $this->getjs($position, $html);
			case 'link': $this->js[] = JS::create($position, $html)->setLink($js); break;
			case 'src': $this->js[] = JS::create($position, $html)->setScript($js); break;
			default: call_user_func_array(array($this, "js"), array_merge(array("link"),func_get_args())); break;
		}
	}
	public function getcss($media=null, $position=null, $html="html", $env="dev"){
		$html = $html==="html";
		usort($this->css, array( __NAMESPACE__ . "\\css", "compare"));
		$string = "\n";		

		foreach($this->css as $css){
			
			if(null!==$media and $media!==$css->media)continue;

			if(null!==$position and $position!==$css->position)continue;
			
			if($css->isLink()){
				$link = is_array($css->link) ? $css->link[$env] : $css->link;
				if($html)
					$string .= "\t\t".'<link media="'.$css->media.'" rel="stylesheet" type="text/css" href="'.$link.'" />'."\n";
				else
					$string .= $link."\n";
			}else{
				if($html and !$css->html)
					$string .= "\t\t".'<style media="'.$css->media.'">'."\n".$css->rules."\n\t\t</style>";
				else
					$string .= $css->rules."\n";
			}
		}		
		return $string;
	}
	public function getjs($position=null, $html="html", $env="dev"){
		$html = $html==="html";
		usort($this->js, array(__NAMESPACE__."\\js", "compare"));
		$string = "\n";

		foreach($this->js as $js){
			
			if(null!==$position and $position!==$js->position) continue;
			
			if($js->isLink()){
				$link = is_array($js->link) ? $js->link[$env] : $js->link;
				if($html)
					$string .= "\t\t".'<script language="javascript" type="text/javascript" src="'.$link.'"></script>'."\n";
				else
					$string .= $link."\n";
			}else{
				if($html and !$js->html)
					$string .= "\t\t".'<script language="javascript" type="text/javascript">'."\n".$js->script."\n\t\t</script>";
				else
					$string .= $js->script."\n";
			}
		}
		return trim($string);
	}

	public function getName()
	{
		return 'twig.extension.layout';
	}
	
}
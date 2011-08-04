<?php

namespace Hypo\LayoutBundle\DependencyInjection;

use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\DependencyInjection\Container;

class css{
	public $position=null;
	public $link=null;
	public $rules=null;
	public $media="all";
	public function __construct($link=null, $rules=null, $media="all", $position=null){
		$this->link = $link;
		$this->rules = $rules;
		$this->media = $media;
		$this->position = $position;
	}
	static function compare(css $css1, css $css2){
		if($css1->rules === null && $css2->rules !== null) return  1;
		if($css1->rules !== null && $css2->rules === null) return -1;
		$cpMedia = strcmp($css1->media, $css2->media);
		if($cpMedia===0){
			if($css1->position === $css2->position ) return 0;
			return $css1->position < $css2->position ? -1 : 1;
		}
		return $cpMedia;			
	}
}
class js{
	public $position=null;
	public $link=null;
	public $rules=null;
	public function __construct($link=null, $rules=null, $position=null){
		$this->link = $link;
		$this->rules = $rules;
		$this->position = $position;
	}
	static function compare(js $js1, js $js2){
		if($js1->position === $js2->position ) return 0;
		return $js1->position < $js2->position ? -1 : 1;
	}
}

class LayoutTwigExtension extends \Twig_Extension {
	
	protected $css = array();
	protected $js = array();

    public function __construct()
    {
    }
    
    public function getFilters()
    {
        return array(
            'addcss'  => new \Twig_Filter_Method($this, 'addcss', array('is_safe' => array('html'))),
            'addjs'  => new \Twig_Filter_Method($this, 'addjs', array('is_safe' => array('html'))),
            'getcss'  => new \Twig_Filter_Method($this, 'getcss', array('is_safe' => array('html'))),
            'getjs'  => new \Twig_Filter_Method($this, 'getjs', array('is_safe' => array('html'))),
        );
    }

    public function getFunctions()
    {
        return $this->getFilters();
    }

    public function addcss($css, $media="all", $position=null)
    {
        $this->css[] = new css($css, null, $media, $position);
    }
    public function addjs($js, $position=null)
    {
        $this->js[] = new js($js, null, $position);
    }
	public function getcss($html=true, $media=false, $position=false){
		usort($this->css, array(__NAMESPACE__."\\css", "compare"));
		$string = "\n";

		foreach($this->css as $css){
			if(false!==$media and $media!==$css->media)continue;
			if(false!==$position and $position!==$css->position)continue;
			if($css->rules!=null and $html)
				$string .= "\t\t".'<style media="'.$css->media.'">'."\n".$css->rules."\n\t\t</style>";
			elseif($css->rules==null and $html)
				$string .= "\t\t".'<link media="'.$css->media.'" rel="stylesheet" type="text/css" href="'.$css->link.'" />'."\n";
			elseif(!$html and $css->rules==null)
				$string .= $css->link;
		}		
		return $string;
	}
	public function getjs($html=true, $position=false){
		usort($this->js, array(__NAMESPACE__."\\js", "compare"));
		$string = "\n";

		foreach($this->js as $js){
			if(false!==$position and $position!==$js->position) continue;
			if($js->rules!=null and $html)
				$string .= "\t\t".'<script language="javascript" type="text/javascript">'."\n".$js->rules."\n\t\t</script>";
			elseif($js->rules==null and $html)
				$string .= "\t\t".'<script language="javascript" type="text/javascript" src="'.$js->link.'"></script>'."\n";
			elseif(!$html and $js->rules==null)
				$string .= $js->link;
		}
		return $string;
	}	
    
    public function getName()
    {
        return 'twig.extension.layout';
    }
    
}
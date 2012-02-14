<?php

namespace Hypo\DocumentBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Hypo\DocumentBundle\Entity\File
 * @ORM\MappedSuperClass
 * @ORM\Table(name="DocumentImage")
 * @ORM\HasLifecycleCallbacks
 */
class Image extends File
{
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $width;	
	public function getWidth(){ return $this->width;}
	public function setWidth($width){ $this->width = $width;}
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $height;
    public function getHeight(){ return $this->height;}
	public function setHeight($height){  $this->height = $height;}
	/**
     * @Assert\File(maxSize="6000000")
     */
	protected $file;
	public function upload()
	{
		parent::upload();
		list($width, $height, $type, $attr) = getimagesize($this->getAbsolutePath()); 
		$this->setWidth($width);
		$this->setHeight($height);
	}

}

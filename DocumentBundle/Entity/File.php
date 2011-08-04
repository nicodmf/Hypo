<?php

namespace Hypo\DocumentBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Hypo\DocumentBundle\Entity\File
 * @ORM\MappedSuperClass
 * @ORM\HasLifecycleCallbacks
 */
class File
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
	public function getId(){ return $this->id;}
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $path;	
	public function getPath(){ return $this->path;}
	public function setPath($path){ $this->path = $path;}
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $mimetype;
    public function getMimetype(){ return $this->mimetype;}
	public function setMimetype($mimetype){  $this->mimetype = $mimetype;}
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $originalName;
    public function getOriginalName(){ return $this->originalName;}
	public function setOriginalName($originalName){  $this->originalName = $originalName;}
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $size;
    public function getSize(){ return $this->size;}
	public function setSize($size){  $this->size = $size;}
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $legend;
    public function setLegend($legend){ $this->legend = $legend;}
    public function getLegend(){ return $this->legend;}
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;
	public function setDescription($description){ $this->description = $description;}
    public function getDescription(){ return $this->description;}
	/**
     * @Assert\File(maxSize="6000000")
     */
	protected $file;
	
	public function getFile(){return $this->file;}
	public function setFile($file){
		if(null !== $file && $this->path !== $this->createPath($file))
			$this->setPath(md5(date("u").rand()));
		$this->file=$file;
	}	
    public function getAbsolutePath()
    {
        return null === $this->path ? null : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path ? null : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'uploads/file';
    }
	protected function createPath($file){
		if (null === $file) return;
		return $file->getClientOriginalName();
	}
	public function upload()
	{
		if (null === $this->file) {
			return;
		}
		
		$mimetype = $this->file->getMimeType();
		
		$this->file->move($this->getUploadRootDir(), $this->file->getClientOriginalName());

		$this->setMimetype($mimetype);
		$this->setOriginalName($this->file->getClientOriginalName());
		$this->setSize($this->file->getClientSize());
		$this->setPath($this->createPath($this->file));

		unset($this->file);
	}
	
	/**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->file) {
            $this->setPath(uniqid().'.'.$this->file->guessExtension());
			$this->upload();
        }
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }	
}
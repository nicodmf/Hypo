<?php

namespace Hypo\TestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Hypo\TestBundle\Entity\Image
 * @ORM\Entity
 * @ORM\Table("TestDocumentImage")
 */
class Image extends \Hypo\DocumentBundle\Entity\Image
{
	protected $file;
	protected $rootDir = 'uploads/documents';
	protected function getUploadRootDir(){ return __DIR__.'/../../../../web/'.$this->getUploadDir();}
    protected function     getUploadDir(){ return 'uploads/images';}
}

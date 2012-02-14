<?php

namespace Hypo\TestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Hypo\TestBundle\Entity\File
 * @ORM\Entity
 * @ORM\Table(name="DocumentTestFile")
 */
class File extends \Hypo\DocumentBundle\Entity\File
{
	protected $file;
	protected $rootDir = 'uploads/documents';
	protected function getUploadRootDir(){ return __DIR__.'/../../../../web/'.$this->getUploadDir();}
    protected function     getUploadDir(){ return 'uploads/documents';}
}

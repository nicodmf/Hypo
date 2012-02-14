<?php

namespace Hypo\DocumentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Hypo\DocumentBundle\Entity\DocumentCollection
 *
 * @ORM\Table(name="DocumentCollections")
 * @ORM\Entity
 */
class DocumentCollection
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string $path
     *
     * @ORM\Column(name="path", type="string", length=255, nullable=true)
     */
    private $path;

    /**
     * @var string $description
     *
     * @ORM\Column(name="description", type="string", nullable=true)
     */
    private $description;

    /**
     * @var string $AuthorizedMimeTypes
     *
     * @ORM\Column(name="AuthorizedMimeTypes", type="string", nullable=true)
     */
    private $AuthorizedMimeTypes;



    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set path
     *
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set AuthorizedMimeTypes
     *
     * @param string $authorizedMimeTypes
     */
    public function setAuthorizedMimeTypes($authorizedMimeTypes)
    {
        $this->AuthorizedMimeTypes = $authorizedMimeTypes;
    }

    /**
     * Get AuthorizedMimeTypes
     *
     * @return string 
     */
    public function getAuthorizedMimeTypes()
    {
        return $this->AuthorizedMimeTypes;
    }
}

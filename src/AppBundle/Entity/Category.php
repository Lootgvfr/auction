<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="category")
 */
class Category
{
	/**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;
	
	/**
	 * @ORM\OneToMany(targetEntity="Property", mappedBy="category")
	 */
	 private $properties;
	 
	/**
	 * @ORM\OneToMany(targetEntity="Lot", mappedBy="category")
	 */
	private $lots;
	
	function __construct() 
	{
		$this->properties = new ArrayCollection();
		$this->lots = new ArrayCollection();
	}
	
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
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
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
     * Add properties
     *
     * @param \AppBundle\Entity\Property $properties
     * @return Category
     */
    public function addProperty(\AppBundle\Entity\Property $properties)
    {
        $this->properties[] = $properties;
    
        return $this;
    }

    /**
     * Remove properties
     *
     * @param \AppBundle\Entity\Property $properties
     */
    public function removeProperty(\AppBundle\Entity\Property $properties)
    {
        $this->properties->removeElement($properties);
    }

    /**
     * Get properties
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Add lots
     *
     * @param \AppBundle\Entity\Lot $lots
     * @return Category
     */
    public function addLot(\AppBundle\Entity\Lot $lots)
    {
        $this->lots[] = $lots;
    
        return $this;
    }

    /**
     * Remove lots
     *
     * @param \AppBundle\Entity\Lot $lots
     */
    public function removeLot(\AppBundle\Entity\Lot $lots)
    {
        $this->lots->removeElement($lots);
    }

    /**
     * Get lots
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLots()
    {
        return $this->lots;
    }
}

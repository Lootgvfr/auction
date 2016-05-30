<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @ORM\Entity
 * @ORM\Table(name="regular")
 */
class Regular
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
     * @ORM\Column(type="string", length=100, name="expr")
     */
    private $exp;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Property")
	 * @ORM\JoinColumn(name="property_id", referencedColumnName="id", onDelete="CASCADE")
     */
	private $property;

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
     * @return Regular
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
     * Set exp
     *
     * @param float $exp
     * @return Regular
     */
    public function setExp($exp)
    {
        $this->exp = $exp;
    
        return $this;
    }

    /**
     * Get exp
     *
     * @return float 
     */
    public function getExp()
    {
        return $this->exp;
    }

    /**
     * Set property
     *
     * @param \AppBundle\Entity\Property $property
     * @return Regular
     */
    public function setProperty(\AppBundle\Entity\Property $property = null)
    {
        $this->property = $property;
    
        return $this;
    }

    /**
     * Get property
     *
     * @return \AppBundle\Entity\Property 
     */
    public function getProperty()
    {
        return $this->property;
    }
}

<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @ORM\Entity
 * @ORM\Table(name="value")
 */
class Value
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
    private $val;
	
	/**
     * @ORM\ManyToOne(targetEntity="Property")
	 * @ORM\JoinColumn(name="property_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $property;
	
	/**
     * @ORM\ManyToOne(targetEntity="Lot")
	 * @ORM\JoinColumn(name="lot_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $lot;

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
     * Set val
     *
     * @param string $val
     * @return Value
     */
    public function setVal($val)
    {
        $this->val = $val;
    
        return $this;
    }

    /**
     * Get val
     *
     * @return string 
     */
    public function getVal()
    {
        return $this->val;
    }

    /**
     * Set property
     *
     * @param \AppBundle\Entity\Property $property
     * @return Value
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

    /**
     * Set lot
     *
     * @param \AppBundle\Entity\Lot $lot
     * @return Value
     */
    public function setLot(\AppBundle\Entity\Lot $lot = null)
    {
        $this->lot = $lot;
    
        return $this;
    }

    /**
     * Get lot
     *
     * @return \AppBundle\Entity\Lot 
     */
    public function getLot()
    {
        return $this->lot;
    }
}

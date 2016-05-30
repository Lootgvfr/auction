<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @ORM\Entity
 * @ORM\Table(name="currency")
 */
class Currency
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
     * @ORM\Column(type="float", name="course")
     */
    private $course;
	
	/**
	 * @ORM\OneToMany(targetEntity="Lot", mappedBy="currency")
	 */
	private $lots;
	
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->lots = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Currency
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
     * Set course
     *
     * @param float $course
     * @return Currency
     */
    public function setCourse($course)
    {
        $this->course = $course;
    
        return $this;
    }

    /**
     * Get course
     *
     * @return float 
     */
    public function getCourse()
    {
        return $this->course;
    }

    /**
     * Add lots
     *
     * @param \AppBundle\Entity\Lot $lots
     * @return Currency
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

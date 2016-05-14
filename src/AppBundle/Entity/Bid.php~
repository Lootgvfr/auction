<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @ORM\Entity
 * @ORM\Table(name="bid")
 */
class Bid
{
	/**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
	
	/**
     * @ORM\Column(type="float")
     */
    private $value;
	
	/**
     * @ORM\Column(type="datetime")
     */
    private $date;
	
	/**
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $user;
	
	/**
     * @ORM\ManyToOne(targetEntity="Lot")
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
     * Set value
     *
     * @param float $value
     * @return Bid
     */
    public function setValue($value)
    {
        $this->value = $value;
    
        return $this;
    }

    /**
     * Get value
     *
     * @return float 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Bid
     */
    public function setDate($date)
    {
        $this->date = $date;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set lot
     *
     * @param \AppBundle\Entity\Lot $lot
     * @return Bid
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

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     * @return Bid
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}

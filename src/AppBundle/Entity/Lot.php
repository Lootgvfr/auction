<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @ORM\Entity
 * @ORM\Table(name="lot")
 */
class Lot
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
     * @ORM\Column(type="string", length=500)
     */
    private $description;
	
	/**
     * @ORM\Column(type="string", length=30)
     */
    private $status;
	
	/**
     * @ORM\Column(type="float", name="buyout_price", nullable=true)
     */
    private $buyoutPrice;
	
	/**
     * @ORM\Column(type="float", name="start_price")
     */
    private $startPrice;
	
	/**
     * @ORM\Column(type="datetime", name="start_date")
     */
    private $startDate;
	
	/**
     * @ORM\Column(type="datetime", name="end_date")
     */
    private $endDate;
	
	/**
	 * @ORM\OneToMany(targetEntity="Value", mappedBy="lot")
	 */
	private $values;
	
	/**
	 * @ORM\OneToMany(targetEntity="Bid", mappedBy="lot")
	 */
	private $bids;
	 
	function __construct()
	{
		$this->values = new ArrayCollection();
		$this->bids = new ArrayCollection();
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
     * @return Lot
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
     * Set description
     *
     * @param string $description
     * @return Lot
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
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
     * Set status
     *
     * @param string $status
     * @return Lot
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set buyoutPrice
     *
     * @param float $buyoutPrice
     * @return Lot
     */
    public function setBuyoutPrice($buyoutPrice)
    {
        $this->buyoutPrice = $buyoutPrice;
    
        return $this;
    }

    /**
     * Get buyoutPrice
     *
     * @return float 
     */
    public function getBuyoutPrice()
    {
        return $this->buyoutPrice;
    }

    /**
     * Set startPrice
     *
     * @param float $startPrice
     * @return Lot
     */
    public function setStartPrice($startPrice)
    {
        $this->startPrice = $startPrice;
    
        return $this;
    }

    /**
     * Get startPrice
     *
     * @return float 
     */
    public function getStartPrice()
    {
        return $this->startPrice;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return Lot
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    
        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime 
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     * @return Lot
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    
        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime 
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Add values
     *
     * @param \AppBundle\Entity\Value $values
     * @return Lot
     */
    public function addValue(\AppBundle\Entity\Value $values)
    {
        $this->values[] = $values;
    
        return $this;
    }

    /**
     * Remove values
     *
     * @param \AppBundle\Entity\Value $values
     */
    public function removeValue(\AppBundle\Entity\Value $values)
    {
        $this->values->removeElement($values);
    }

    /**
     * Get values
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * Add bids
     *
     * @param \AppBundle\Entity\Bid $bids
     * @return Lot
     */
    public function addBid(\AppBundle\Entity\Bid $bids)
    {
        $this->bids[] = $bids;
    
        return $this;
    }

    /**
     * Remove bids
     *
     * @param \AppBundle\Entity\Bid $bids
     */
    public function removeBid(\AppBundle\Entity\Bid $bids)
    {
        $this->bids->removeElement($bids);
    }

    /**
     * Get bids
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBids()
    {
        return $this->bids;
    }
}

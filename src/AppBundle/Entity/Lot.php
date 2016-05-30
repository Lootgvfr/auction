<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;


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
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", onDelete="CASCADE")
     */
	private $category;
	
	/**
     * @ORM\ManyToOne(targetEntity="Currency")
     * @ORM\JoinColumn(name="currency_id", referencedColumnName="id")
     */
	private $currency;
	
	/**
	 * @ORM\OneToMany(targetEntity="Value", mappedBy="lot")
	 */
	private $values;
	
	/**
	 * @ORM\OneToMany(targetEntity="Bid", mappedBy="lot")
	 */
	private $bids;
	
	/**
	 * @ORM\OneToMany(targetEntity="CommentLot", mappedBy="lot")
	 */
	private $comments;
	
	/**
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(name="author_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	private $author;
	
	/**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $path;
	
	private $currentPrice;
	
	/**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $img;
	
	public function setImg($img)
	{
		$this->img = $img;
		
		return $this;
	}
	
	public function getImg()
	{
		return $this->img;
	}
	
	public function getWebPath()
    {
        return null === $this->path
            ? null
            : '/'.$this->getUploadDir().'/'.$this->path;
    }
	
    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/lots';
    }
	
	/**
     * @Assert\File(maxSize="6000000")
     */
    private $file;

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }
	
	public function upload()
	{
		if (null === $this->getFile()) {
			return;
		}
		
			$this->getFile()->move(
			$this->getUploadDir(),
			$this->getFile()->getClientOriginalName()
		);

		$this->path = $this->getFile()->getClientOriginalName();
		$this->setImg($this->getWebPath());
		
		$this->file = null;
	}
	 
	function __construct()
	{
		$this->img = $this->getWebPath();
		$this->values = new ArrayCollection();
		$this->bids = new ArrayCollection();
		$this->comments = new ArrayCollection();
	}
	
	public function setCurrentPrice($currentPrice)
	{
		$this->currentPrice = $currentPrice;
	}

	public function getCurrentPrice()
	{
		$bids = $this->bids;
		if (count($bids) == 0)
		{
			$this->currentPrice = $this->startPrice;
			return $this->currentPrice;
		}
		else
		{
			$this->currentPrice = $bids[count($bids)-1]->getValue();
			return $this->currentPrice;
		}
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

    /**
     * Set category
     *
     * @param \AppBundle\Entity\Category $category
     * @return Lot
     */
    public function setCategory(\AppBundle\Entity\Category $category = null)
    {
        $this->category = $category;
    
        return $this;
    }

    /**
     * Get category
     *
     * @return \AppBundle\Entity\Category 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set author
     *
     * @param \AppBundle\Entity\User $author
     * @return Lot
     */
    public function setAuthor(\AppBundle\Entity\User $author = null)
    {
        $this->author = $author;
    
        return $this;
    }

    /**
     * Get author
     *
     * @return \AppBundle\Entity\User 
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return Lot
     */
    public function setPath($path)
    {
        $this->path = $path;
    
        return $this;
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
     * Add comments
     *
     * @param \AppBundle\Entity\CommentLot $comments
     * @return Lot
     */
    public function addComment(\AppBundle\Entity\CommentLot $comments)
    {
        $this->comments[] = $comments;
    
        return $this;
    }

    /**
     * Remove comments
     *
     * @param \AppBundle\Entity\CommentLot $comments
     */
    public function removeComment(\AppBundle\Entity\CommentLot $comments)
    {
        $this->comments->removeElement($comments);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set currency
     *
     * @param \AppBundle\Entity\Currency $currency
     * @return Lot
     */
    public function setCurrency(\AppBundle\Entity\Currency $currency = null)
    {
        $this->currency = $currency;
    
        return $this;
    }

    /**
     * Get currency
     *
     * @return \AppBundle\Entity\Currency 
     */
    public function getCurrency()
    {
        return $this->currency;
    }
}

<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="comment_user")
 */
class CommentUser
{
	/**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
	
	/**
     * @ORM\Column(type="string", length=500)
     */
    private $text;
	
	/**
     * @ORM\Column(type="string", length=30)
     */
    private $status;
	
	/**
     * @ORM\Column(type="datetime")
     */
    private $date;
	
	/**
     * @ORM\Column(type="integer")
     */
	private $rating;
	
	/**
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(name="seller_id", referencedColumnName="id")
     */
    private $seller;
	
	/**
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     */
    private $author;

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
     * Set text
     *
     * @param string $text
     * @return CommentUser
     */
    public function setText($text)
    {
        $this->text = $text;
    
        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return CommentUser
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
     * Set date
     *
     * @param \DateTime $date
     * @return CommentUser
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
     * Set rating
     *
     * @param integer $rating
     * @return CommentUser
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    
        return $this;
    }

    /**
     * Get rating
     *
     * @return integer 
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set seller
     *
     * @param \AppBundle\Entity\User $seller
     * @return CommentUser
     */
    public function setSeller(\AppBundle\Entity\User $seller = null)
    {
        $this->seller = $seller;
    
        return $this;
    }

    /**
     * Get seller
     *
     * @return \AppBundle\Entity\User 
     */
    public function getSeller()
    {
        return $this->seller;
    }

    /**
     * Set author
     *
     * @param \AppBundle\Entity\User $author
     * @return CommentUser
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
}

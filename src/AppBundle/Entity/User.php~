<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 * @UniqueEntity(fields="email", message="Email already taken")
 * @UniqueEntity(fields="username", message="Username already taken")
 */
class User implements UserInterface
{
	/**
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
	private $id;
	
	/**
     * @ORM\Column(type="string", length=25, unique=true)
	 * @Assert\NotBlank()
     */
    private $username;
	
	/**
     * @ORM\Column(type="string", length=64)
     */
    private $password;
	
	/**
     * @Assert\NotBlank()
     * @Assert\Length(max=4096)
     */
    private $plainPassword;
	
	/**
     * @ORM\Column(type="string", length=60, unique=true)
	 * @Assert\NotBlank()
     * @Assert\Email()
     */
	private $email;
	
	/**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
	private $name;
	
	/**
     * @ORM\Column(type="string", length=30, name="group_name")
     */
	private $group;
	
	/**
	 * @ORM\OneToMany(targetEntity="CommentUser", mappedBy="seller")
	 */
	private $user_comments_to;
	 
	/**
	 * @ORM\OneToMany(targetEntity="CommentUser", mappedBy="author")
	 */
	private $user_comments_by;
	
	/**
	 * @ORM\OneToMany(targetEntity="CommentLot", mappedBy="author")
	 */
	private $lot_comments_by;
	
	/**
	 * @ORM\OneToMany(targetEntity="Bid", mappedBy="user")
	 */
	private $bids;
	
	function __construct() 
	{
		$this->properties = new ArrayCollection();
		$this->user_comments_to = new ArrayCollection();
		$this->user_comments_by = new ArrayCollection();
		$this->lot_comments_by = new ArrayCollection();
		$this->bids = new ArrayCollection();
	}
	
	public function getSalt()
    {
        return null;
    }
	
	public function getRoles()
    {
        return array('ROLE_USER');
    }

    public function eraseCredentials()
    {
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
        ) = unserialize($serialized);
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
     * Set username
     *
     * @param string $login
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;
    
        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;
    
        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }
	
	public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return User
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
     * Set group
     *
     * @param string $group
     * @return User
     */
    public function setGroup($group)
    {
        $this->group = $group;
    
        return $this;
    }

    /**
     * Get group
     *
     * @return string 
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Add user_comments_to
     *
     * @param \AppBundle\Entity\CommentUser $userCommentsTo
     * @return User
     */
    public function addUserCommentsTo(\AppBundle\Entity\CommentUser $userCommentsTo)
    {
        $this->user_comments_to[] = $userCommentsTo;
    
        return $this;
    }

    /**
     * Remove user_comments_to
     *
     * @param \AppBundle\Entity\CommentUser $userCommentsTo
     */
    public function removeUserCommentsTo(\AppBundle\Entity\CommentUser $userCommentsTo)
    {
        $this->user_comments_to->removeElement($userCommentsTo);
    }

    /**
     * Get user_comments_to
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserCommentsTo()
    {
        return $this->user_comments_to;
    }

    /**
     * Add user_comments_by
     *
     * @param \AppBundle\Entity\CommentUser $userCommentsBy
     * @return User
     */
    public function addUserCommentsBy(\AppBundle\Entity\CommentUser $userCommentsBy)
    {
        $this->user_comments_by[] = $userCommentsBy;
    
        return $this;
    }

    /**
     * Remove user_comments_by
     *
     * @param \AppBundle\Entity\CommentUser $userCommentsBy
     */
    public function removeUserCommentsBy(\AppBundle\Entity\CommentUser $userCommentsBy)
    {
        $this->user_comments_by->removeElement($userCommentsBy);
    }

    /**
     * Get user_comments_by
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserCommentsBy()
    {
        return $this->user_comments_by;
    }

    /**
     * Add lot_comments_by
     *
     * @param \AppBundle\Entity\CommentLot $lotCommentsBy
     * @return User
     */
    public function addLotCommentsBy(\AppBundle\Entity\CommentLot $lotCommentsBy)
    {
        $this->lot_comments_by[] = $lotCommentsBy;
    
        return $this;
    }

    /**
     * Remove lot_comments_by
     *
     * @param \AppBundle\Entity\CommentLot $lotCommentsBy
     */
    public function removeLotCommentsBy(\AppBundle\Entity\CommentLot $lotCommentsBy)
    {
        $this->lot_comments_by->removeElement($lotCommentsBy);
    }

    /**
     * Get lot_comments_by
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLotCommentsBy()
    {
        return $this->lot_comments_by;
    }

    /**
     * Add bids
     *
     * @param \AppBundle\Entity\Bid $bids
     * @return User
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

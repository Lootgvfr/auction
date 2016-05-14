<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
	
	/**
	 * @ORM\OneToMany(targetEntity="Lot", mappedBy="author")
	 */
	private $lots;
	
	function __construct() 
	{
		$this->user_comments_to = new ArrayCollection();
		$this->user_comments_by = new ArrayCollection();
		$this->lot_comments_by = new ArrayCollection();
		$this->bids = new ArrayCollection();
		$this->lots = new ArrayCollection();
	}
	
	/**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
	private $address;
	
	/**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
	private $phone;
	
	/**
     * @ORM\Column(type="text", length=2000, nullable=true)
     */
	private $info;
	
	 /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $path_image;
	
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

	
	public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/documents';
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
     * Set address
     *
     * @param string $address
     * @return User
     */
    public function setAddress($address)
    {
        $this->address = $address;
    
        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    
        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set info
     *
     * @param string $info
     * @return User
     */
    public function setInfo($info)
    {
        $this->info = $info;
    
        return $this;
    }

    /**
     * Get info
     *
     * @return string 
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Set path_image
     *
     * @param string $pathImage
     * @return User
     */
    public function setPathImage($pathImage)
    {
        $this->path_image = $pathImage;
    
        return $this;
    }

    /**
     * Get path_image
     *
     * @return string 
     */
    public function getPathImage()
    {
        return $this->path_image;
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

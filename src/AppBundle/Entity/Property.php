<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @ORM\Entity
 * @ORM\Table(name="property")
 */
class Property
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
     * @ORM\Column(type="string", length=100, name="p_range")
     */
    private $range;
	
	/**
     * @ORM\Column(type="string", length=100)
     */
    private $example;
	
	/**
     * @ORM\Column(type="boolean", name="is_nullable", options={"default": true})
     */
    private $isNullable;
	
	/**
     * @ORM\ManyToOne(targetEntity="Category")
	 * @ORM\JoinColumn(name="category_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $category;
	
	/**
	 * @ORM\OneToMany(targetEntity="Regular", mappedBy="property")
	 */
    private $regulars; 
	
	/**
	 * @ORM\OneToMany(targetEntity="Value", mappedBy="property")
	 */
	 private $values;
	 
	function __construct()
	{
		$this->values = new ArrayCollection();
		$this->regulars = new ArrayCollection();
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
     * @return Property
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
     * Set range
     *
     * @param string $range
     * @return Property
     */
    public function setRange($range)
    {
        $this->range = $range;
    
        return $this;
    }

    /**
     * Get range
     *
     * @return string 
     */
    public function getRange()
    {
        return $this->range;
    }

    /**
     * Set category
     *
     * @param \AppBundle\Entity\Category $category
     * @return Property
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
     * Add values
     *
     * @param \AppBundle\Entity\Value $values
     * @return Property
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
     * Set regexp
     *
     * @param string $regexp
     * @return Property
     */
    public function setRegexp($regexp)
    {
        $this->regexp = $regexp;
    
        return $this;
    }

    /**
     * Get regexp
     *
     * @return string 
     */
    public function getRegexp()
    {
        return $this->regexp;
    }

    /**
     * Set example
     *
     * @param string $example
     * @return Property
     */
    public function setExample($example)
    {
        $this->example = $example;
    
        return $this;
    }

    /**
     * Get example
     *
     * @return string 
     */
    public function getExample()
    {
        return $this->example;
    }

    /**
     * Set is_nullable
     *
     * @param \bool $isNullable
     * @return Property
     */
    public function setIsNullable($isNullable)
    {
        $this->isNullable = $isNullable;
    
        return $this;
    }

    /**
     * Get is_nullable
     *
     * @return \bool 
     */
    public function getIsNullable()
    {
        return $this->isNullable;
    }

    /**
     * Add regulars
     *
     * @param \AppBundle\Entity\Regular $regulars
     * @return Property
     */
    public function addRegular(\AppBundle\Entity\Regular $regulars)
    {
        $this->regulars[] = $regulars;
    
        return $this;
    }

    /**
     * Remove regulars
     *
     * @param \AppBundle\Entity\Regular $regulars
     */
    public function removeRegular(\AppBundle\Entity\Regular $regulars)
    {
        $this->regulars->removeElement($regulars);
    }

    /**
     * Get regulars
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRegulars()
    {
        return $this->regulars;
    }
}

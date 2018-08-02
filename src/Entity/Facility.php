<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="facility")
 * @ORM\Entity(repositoryClass="App\Repository\FacilityRepository")
 */
class Facility
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $totalParts;




    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getTotalParts()
    {
        return $this->totalParts;
    }

    /**
     * @param mixed $totalParts
     */
    public function setTotalParts($totalParts): void
    {
        $this->totalParts = $totalParts;
    }



}

<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="facility")
 * @ORM\Entity(repositoryClass="App\Repository\FacilityRepository")
 */
class Facility
{

    const TYPE_POOL = 0;
    const TYPE_HALL = 1;
    const TYPE_FOOTBALL_PLAYGROUND = 2;
    const TYPE_COURT = 3;
    const TYPE_STAGE = 4;

    const TYPES = [
        self::TYPE_POOL => "Swimming Pool",
        self::TYPE_HALL => "Sport Hall",
        self::TYPE_FOOTBALL_PLAYGROUND => "Football Playground",
        self::TYPE_COURT => "Court",
        self::TYPE_COURT => "Stage",
    ];

    const PARTS = [
        self::TYPE_POOL => [
            "0" => "1 lane",
            "1" => "2 lanes",
            "2" => "3 lanes",
            "3" => "4 lanes",
            "4" => "5 lanes",
            "5" => "6 lanes",
            "6" => "7 lanes",
            "7" => "8 lanes",
        ],
        self::TYPE_HALL => [
            '0' => "All",
            '1' => "1/2",
            '2' => "1/3",
        ],
        self::TYPE_FOOTBALL_PLAYGROUND => [
            '0' => "All",
            '1' => "1/2",
        ],
        self::TYPE_COURT => [
            '0' => "All"
        ],
        self::TYPE_STAGE => [
            '0' => "All"
        ]
    ];


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
    private $type;

    /**
     * @var Schedule[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Schedule", mappedBy="facility")
     */
    private $schedules;

    /**
     * @return mixed
     */
    public function __toString() {
        return $this->name;
    }

    /**
     * @return mixed
     */
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
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return Schedule[]
     */
    public function getSchedules()
    {
        return $this->schedules;
    }




}

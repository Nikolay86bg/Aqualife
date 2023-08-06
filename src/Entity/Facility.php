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
    const TYPE_DIVISIBLE_HALL = 5;
    const TYPE_BEACH_FIELD = 6;
    const TYPE_DIVISIBLE_HALL_BY_4 = 7;

    const TYPES = [
        self::TYPE_POOL => "Swimming Pool",
        self::TYPE_HALL => "Sport Hall",
        self::TYPE_FOOTBALL_PLAYGROUND => "Football Playground",
        self::TYPE_COURT => "Court",
        self::TYPE_STAGE => "Stage",
        self::TYPE_DIVISIBLE_HALL => "Divisible Sport Hall",
        self::TYPE_BEACH_FIELD => "Beach Field",
        self::TYPE_DIVISIBLE_HALL_BY_4 => "Divisible Sport Hall By 4",
    ];

    const TYPE_OPTION_ALL = 0;
    const TYPE_OPTION_12A = 1;
    const TYPE_OPTION_12B = 2;
    const TYPE_OPTION_13A = 3;
    const TYPE_OPTION_13B = 4;
    const TYPE_OPTION_13C = 5;
    const TYPE_OPTION_14A = 6;
    const TYPE_OPTION_14B = 7;
    const TYPE_OPTION_14C = 8;
    const TYPE_OPTION_14D = 9;

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
            self::TYPE_OPTION_ALL => "All",
        ],
        self::TYPE_FOOTBALL_PLAYGROUND => [
            self::TYPE_OPTION_ALL => "All",
            self::TYPE_OPTION_12A => "1/2 A",
            self::TYPE_OPTION_12B => "1/2 B",
        ],
        self::TYPE_COURT => [
            self::TYPE_OPTION_ALL => "All",
        ],
        self::TYPE_STAGE => [
            self::TYPE_OPTION_ALL => "All",
        ],
        self::TYPE_DIVISIBLE_HALL => [
            self::TYPE_OPTION_ALL => "All",
            self::TYPE_OPTION_12A => "1/2 A",
            self::TYPE_OPTION_12B => "1/2 B",
        ],
        self::TYPE_BEACH_FIELD => [
            self::TYPE_OPTION_ALL => "All",
            self::TYPE_OPTION_13A => "1/3 A",
            self::TYPE_OPTION_13B => "1/3 B",
            self::TYPE_OPTION_13C => "1/3 C",
        ],
        self::TYPE_DIVISIBLE_HALL_BY_4 => [
            self::TYPE_OPTION_ALL => "All",
            self::TYPE_OPTION_14A => "1/4 A",
            self::TYPE_OPTION_14B => "1/4 B",
            self::TYPE_OPTION_14C => "1/4 C",
            self::TYPE_OPTION_14D => "1/4 D",
        ],
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

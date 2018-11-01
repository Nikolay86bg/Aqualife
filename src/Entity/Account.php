<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="account")
 * @ORM\Entity(repositoryClass="App\Repository\AccountRepository")
 */
class Account
{
    const SPORT_SWIMMING = 0;
    const SPORT_BADMINTON = 1;
    const SPORT_BOXING = 2;
    const SPORT_WRESTLING = 3;
    const SPORT_DANCING = 4;
    const SPORT_BASKETBALL = 5;
    const SPORT_WATER_POLO = 6;
    const SPORT_MODERN_PENTATHLON = 7;
    const SPORT_BEACH_VOLLEYBALL = 8;
    const SPORT_SYNCHRONISED_SWIMMING = 10;
    const SPORT_TABLE_TENNIS = 11;
    const SPORT_TRIATHLON = 12;
    const SPORT_FENCING = 13;
    const SPORT_FOOTBALL = 14;
    const SPORT_RHYTHMIC_GYMNASTICS = 15;
    const SPORT_HANDBALL = 16;
    const SPORT_CHESS = 17;
    const SPORT_CHECKERS = 18;
    const SPORT_COMBAT_SPORTS = 19;
    const SPORT_OTHERS = 20;

    const SPORTS = [
        self::SPORT_SWIMMING => "sport.swimming",
        self::SPORT_BADMINTON => "sport.badminton",
        self::SPORT_BOXING => "sport.boxing",
        self::SPORT_WRESTLING => "sport.wrestling",
        self::SPORT_DANCING => "sport.dancing",
        self::SPORT_BASKETBALL => "sport.basketball",
        self::SPORT_WATER_POLO => "sport.water_polo",
        self::SPORT_MODERN_PENTATHLON => "sport.modern_pentathlon",
        self::SPORT_BEACH_VOLLEYBALL => "sport.beach_volleyball",
        self::SPORT_SYNCHRONISED_SWIMMING => "sport.synchronised_swimming",
        self::SPORT_TABLE_TENNIS => "sport.table_tennis",
        self::SPORT_TRIATHLON => "sport.triathlon",
        self::SPORT_FENCING => "sport.fencing",
        self::SPORT_FOOTBALL => "sport.football",
        self::SPORT_RHYTHMIC_GYMNASTICS => "sport.rhythmic_gymnastics",
        self::SPORT_HANDBALL => "sport.handball",
        self::SPORT_CHESS => "sport.chess",
        self::SPORT_CHECKERS => "sport.checkers",
        self::SPORT_COMBAT_SPORTS => "sport.combat_sports",
        self::SPORT_OTHERS => "sport.others",
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $manager;

    /**
     * @ORM\Column(type="integer")
     */
    private $sport;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $country;

    /**
     * @var Query
     *
     * * @ORM\OneToOne(targetEntity="App\Entity\Query", mappedBy="account")
     */
    private $query;

    /**
     * @var Schedule[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Schedule", mappedBy="account")
     */
    private $schedules;

    /**
     * @var MealSchedule[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\MealSchedule", mappedBy="account")
     */
    private $mealSchedules;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    private $deletedAt;

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    public function getSport()
    {
        return $this->sport;
    }

    public function setSport($sport)
    {
        $this->sport = $sport;

        return $this;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @param mixed $manager
     */
    public function setManager($manager)
    {
        $this->manager = $manager;
    }

    /**
     * @return Query
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param Query $query
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * @return Schedule[]
     */
    public function getSchedules()
    {
        //return without deleted
        $return = [];
        if ($schedules = $this->schedules) {
            /** @var MealSchedule $meal */
            foreach ($schedules as $schedule) {
                if (!$schedule->getDeleted()) {
                    $return[] = $schedule;
                }
            }
        }

        return $return;
    }

    /**
     * @param Schedule[] $schedules
     */
    public function setSchedules(array $schedules)
    {
        $this->schedules = $schedules;
    }

    /**
     * @return MealSchedule[]
     */
    public function getMealSchedules()
    {
    //return without deleted
        $return = [];
        if ($meals = $this->mealSchedules) {
            /** @var MealSchedule $meal */
            foreach ($meals as $meal) {
                if (!$meal->getDeleted()) {
                    $return[] = $meal;
                }
            }
        }

        return $return;
    }

    /**
     * @param MealSchedule[] $mealSchedules
     */
    public function setMealSchedules(array $mealSchedules)
    {
        $this->mealSchedules = $mealSchedules;
    }



    /**
     * Get schedules information for pool query
     *
     * @return array
     */
    public function isSetPool()
    {
        $return = [];
        if ($schedules = $this->getSchedules()) {
            /** @var Schedule $schedule */
            foreach ($schedules as $schedule) {
                if ($schedule->getFacility()->getType() == Facility::TYPE_POOL) {
                    $return[$schedule->getId()]['date'] = $schedule->getDate();
                    $return[$schedule->getId()]['from'] = $schedule->getTimeFrom();
                    $return[$schedule->getId()]['to'] = $schedule->getTimeTo();
                    $return[$schedule->getId()]['lanes'] = $schedule->getParts();
                    $return[$schedule->getId()]['pool'] = $schedule->getFacility()->getName();
                    $return[$schedule->getId()]['facility'] = $schedule->getFacility();
                }
            }
        }

        return $return;
    }

    /**
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * @param \DateTime $deletedAt
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }

}

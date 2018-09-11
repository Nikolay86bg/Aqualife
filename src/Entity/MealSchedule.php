<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="meal_schedule")
 * @ORM\Entity(repositoryClass="App\Repository\MealScheduleRepository")
 */
class MealSchedule
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @var integer
     *
     * @ORM\Column(name="restaurant", type="integer")
     */
    private $restaurant;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="breakfast_time", type="time", nullable=true)
     */
    private $breakfastTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="middle_breakfast_time", type="time", nullable=true)
     */
    private $middleBreakfastTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lunch_time", type="time", nullable=true)
     */
    private $lunchTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dinner_time", type="time", nullable=true)
     */
    private $dinnerTime;

    /**
     * @var Account
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Account", inversedBy="mealSchedules")
     */
    private $account;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted", type="date", nullable=true)
     */
    private $deleted;


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;
    }

    /**
     * @return Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @param Account $account
     */
    public function setAccount(Account $account)
    {
        $this->account = $account;
    }

    /**
     * @return int
     */
    public function getRestaurant()
    {
        return $this->restaurant;
    }

    /**
     * @param int $restaurant
     */
    public function setRestaurant($restaurant)
    {
        $this->restaurant = $restaurant;
    }

    /**
     * @return \DateTime
     */
    public function getBreakfastTime()
    {
        return $this->breakfastTime;
    }

    /**
     * @param \DateTime $breakfastTime
     */
    public function setBreakfastTime($breakfastTime)
    {
        $this->breakfastTime = $breakfastTime;
    }

    /**
     * @return \DateTime
     */
    public function getMiddleBreakfastTime()
    {
        return $this->middleBreakfastTime;
    }

    /**
     * @param \DateTime $middleBreakfastTime
     */
    public function setMiddleBreakfastTime($middleBreakfastTime)
    {
        $this->middleBreakfastTime = $middleBreakfastTime;
    }

    /**
     * @return \DateTime
     */
    public function getLunchTime()
    {
        return $this->lunchTime;
    }

    /**
     * @param \DateTime $lunchTime
     */
    public function setLunchTime($lunchTime)
    {
        $this->lunchTime = $lunchTime;
    }

    /**
     * @return \DateTime
     */
    public function getDinnerTime()
    {
        return $this->dinnerTime;
    }

    /**
     * @param \DateTime $dinnerTime
     */
    public function setDinnerTime($dinnerTime)
    {
        $this->dinnerTime = $dinnerTime;
    }

    /**
     * @return \DateTime
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param \DateTime $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

}

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
     * @ORM\Column(name="breakfast", type="integer", nullable=true)
     */
    private $breakfast;

    /**
     * @var integer
     *
     * @ORM\Column(name="middle_breakfast", type="integer", nullable=true)
     */
    private $middleBreakfast;

    /**
     * @var integer
     *
     * @ORM\Column(name="lunch", type="integer", nullable=true)
     */
    private $lunch;

    /**
     * @var integer
     *
     * @ORM\Column(name="dinner", type="integer", nullable=true)
     */
    private $dinner;

    /**
     * @var Account
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Account", inversedBy="mealSchedules")
     */
    private $account;


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
     * @return int
     */
    public function getBreakfast()
    {
        return $this->breakfast;
    }

    /**
     * @param int $breakfast
     */
    public function setBreakfast(int $breakfast)
    {
        $this->breakfast = $breakfast;
    }

    /**
     * @return int
     */
    public function getMiddleBreakfast()
    {
        return $this->middleBreakfast;
    }

    /**
     * @param int $middleBreakfast
     */
    public function setMiddleBreakfast(int $middleBreakfast)
    {
        $this->middleBreakfast = $middleBreakfast;
    }

    /**
     * @return int
     */
    public function getLunch()
    {
        return $this->lunch;
    }

    /**
     * @param int $lunch
     */
    public function setLunch(int $lunch)
    {
        $this->lunch = $lunch;
    }

    /**
     * @return int
     */
    public function getDinner()
    {
        return $this->dinner;
    }

    /**
     * @param int $dinner
     */
    public function setDinner(int $dinner)
    {
        $this->dinner = $dinner;
    }

    /**
     * @return Account
     */
    public function getAccount(): Account
    {
        return $this->account;
    }

    /**
     * @param Account $account
     */
    public function setAccount(Account $account): void
    {
        $this->account = $account;
    }






}

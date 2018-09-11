<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="schedule")
 * @ORM\Entity(repositoryClass="App\Repository\ScheduleRepository")
 */
class Schedule
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Facility
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Facility", inversedBy="schedules")
     */
    private $facility;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time_from", type="time")
     */
    private $timeFrom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time_to", type="time")
     */
    private $timeTo;

    /**
     * @var integer
     *
     * @ORM\Column(name="parts", type="integer")
     */
    private $parts;

    /**
     * @var string
     *
     * @ORM\Column(name="lanes", type="string", nullable=true)
     */
    private $lanes;

    /**
     * @var Account
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Account", inversedBy="schedules")
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
     * @return Facility
     */
    public function getFacility()
    {
        return $this->facility;
    }

    /**
     * @param Facility $facility
     */
    public function setFacility(Facility $facility)
    {
        $this->facility = $facility;
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
     * @return \DateTime
     */
    public function getTimeFrom()
    {
        return $this->timeFrom;
    }

    /**
     * @param \DateTime $timeFrom
     */
    public function setTimeFrom(\DateTime $timeFrom)
    {
        $this->timeFrom = $timeFrom;
    }

    /**
     * @return \DateTime
     */
    public function getTimeTo()
    {
        return $this->timeTo;
    }

    /**
     * @param \DateTime $timeTo
     */
    public function setTimeTo(\DateTime $timeTo)
    {
        $this->timeTo = $timeTo;
    }

    /**
     * @return int
     */
    public function getParts()
    {
        return $this->parts;
    }

    /**
     * @param int $parts
     */
    public function setParts(int $parts)
    {
        $this->parts = $parts;
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
     * @return string
     */
    public function getLanes()
    {
        return $this->lanes;
    }

    /**
     * @param string $lanes
     */
    public function setLanes(string $lanes)
    {
        $this->lanes = $lanes;
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

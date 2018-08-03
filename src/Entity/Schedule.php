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
     * @ORM\Column(name="note", type="string", nullable=true)
     */
    private $note;

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
    public function getFacility(): Facility
    {
        return $this->facility;
    }

    /**
     * @param Facility $facility
     */
    public function setFacility(Facility $facility): void
    {
        $this->facility = $facility;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate(\DateTime $date): void
    {
        $this->date = $date;
    }

    /**
     * @return \DateTime
     */
    public function getTimeFrom(): \DateTime
    {
        return $this->timeFrom;
    }

    /**
     * @param \DateTime $timeFrom
     */
    public function setTimeFrom(\DateTime $timeFrom): void
    {
        $this->timeFrom = $timeFrom;
    }

    /**
     * @return \DateTime
     */
    public function getTimeTo(): \DateTime
    {
        return $this->timeTo;
    }

    /**
     * @param \DateTime $timeTo
     */
    public function setTimeTo(\DateTime $timeTo): void
    {
        $this->timeTo = $timeTo;
    }

    /**
     * @return int
     */
    public function getParts(): int
    {
        return $this->parts;
    }

    /**
     * @param int $parts
     */
    public function setParts(int $parts): void
    {
        $this->parts = $parts;
    }

    /**
     * @return string
     */
    public function getNote(): string
    {
        return $this->note;
    }

    /**
     * @param string $note
     */
    public function setNote(string $note): void
    {
        $this->note = $note;
    }


}

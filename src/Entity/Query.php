<?php

namespace App\Entity;

use App\Doctrine\Traits\TimestampableEntityTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="query")
 * @ORM\Entity(repositoryClass="App\Repository\QueryRepository")
 */
class Query
{
    use TimestampableEntityTrait;

    const HOTEL1 = 0;
    const HOTEL2 = 1;
    const HOTEL3 = 2;
    const HOTEL4 = 3;

    const HOTELS = [
        self::HOTEL1 => "Sport",
        self::HOTEL2 => "Active",
        self::HOTEL3 => "Aqualife",
        self::HOTEL4 => "Tower"
    ];

    const STATUS_IN_PROGRESS = 0;
    const STATUS_ACCEPTED = 1;
    const STATUS_REJECTED = 2;

    const STATUSES = [
      self::STATUS_IN_PROGRESS => 'In Progress',
      self::STATUS_ACCEPTED => 'Accepted',
      self::STATUS_REJECTED => 'Rejected',
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Account
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Account", inversedBy="queries")
     */
    private $account;

    /**
    * @var \DateTime
    *
    * @ORM\Column(name="date_of_arrival", type="datetime", nullable=true)
    */
    private $dateOfArrival;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_of_departure", type="datetime", nullable=true)
     */
    private $dateOfDeparture;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", options={"default": 0})
     */
    private $status = self::STATUS_IN_PROGRESS;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="queries")
     */
    private $createdBy;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $hotel;

    /**
     * @return User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param User $createdBy
     */
    public function setCreatedBy(User $createdBy)
    {
        $this->createdBy = $createdBy;
    }


    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getDateOfArrival()
    {
        return $this->dateOfArrival;
    }

    /**
     * @param \DateTime $dateOfArrival
     */
    public function setDateOfArrival(\DateTime $dateOfArrival)
    {
        $this->dateOfArrival = $dateOfArrival;
    }

    /**
     * @return \DateTime
     */
    public function getDateOfDeparture()
    {
        return $this->dateOfDeparture;
    }

    /**
     * @param \DateTime $dateOfDeparture
     */
    public function setDateOfDeparture(\DateTime $dateOfDeparture)
    {
        $this->dateOfDeparture = $dateOfDeparture;
    }

    /**
     * @return int
     */
    public function getHotel()
    {
        return $this->hotel;
    }

    /**
     * @param int $hotel
     */
    public function setHotel(int $hotel)
    {
        $this->hotel = $hotel;
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
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }



}

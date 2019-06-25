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
    const NO_HOTEL = 9;

    const HOTELS = [
        self::NO_HOTEL => "No Hotel",
        self::HOTEL1 => "Sport",
        self::HOTEL2 => "Active",
        self::HOTEL3 => "Aqualife",
        self::HOTEL4 => "Tower",
    ];

    const RESTAURANT1 = 0;
    const RESTAURANT2 = 1;
    const RESTAURANT3 = 2;
    const RESTAURANT4 = 3;

    const RESTAURANTS = [
        self::RESTAURANT1 => "Sport",
        self::RESTAURANT2 => "Active",
        self::RESTAURANT3 => "Aqualife",
        self::RESTAURANT4 => "Tower",
    ];

    const BREAKFAST = 0;
    const MIDDLE_BREAKFAST = 1;
    const LUNCH = 2;
    const DINNER = 3;

    const MEALS = [
        self::BREAKFAST => "Breakfast",
        self::MIDDLE_BREAKFAST => "MIddle Breakfast",
        self::LUNCH => "Lunch",
        self::DINNER => "Dinner",
    ];

    const STATUS_IN_PROGRESS = 0;
    const STATUS_ACCEPTED = 1;
    const STATUS_REJECTED = 2;

    const STATUSES = [
      self::STATUS_IN_PROGRESS => 'In Progress',
      self::STATUS_ACCEPTED => 'Accepted',
      self::STATUS_REJECTED => 'Rejected',
    ];

    const PAYED_NO = 0;
    const PAYED_YES = 1;

    const PAYED = [
        self::PAYED_NO => 'NO',
        self::PAYED_YES => 'YES'
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
     * @ORM\OneToOne(targetEntity="App\Entity\Account", inversedBy="query")
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
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $hotel;

    /**
     * @var integer
     *
     * @ORM\Column(name="payed", type="integer")
     */
    private $payed = self::PAYED_NO;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="number_of_people", type="integer", nullable=true)
     */
    private $numberOfPeople;

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
     * @return string
     */
    public function getHotel()
    {
        return $this->hotel;
    }

    /**
     * @param string $hotel
     */
    public function setHotel(string $hotel)
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

    /**
     * @return int
     */
    public function getPayed()
    {
        return $this->payed;
    }

    /**
     * @param int $payed
     */
    public function setPayed($payed)
    {
        $this->payed = $payed;
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

    /**
     * @return int
     */
    public function getNumberOfPeople()
    {
        return $this->numberOfPeople;
    }

    /**
     * @param int $numberOfPeople
     */
    public function setNumberOfPeople($numberOfPeople)
    {
        $this->numberOfPeople = $numberOfPeople;
    }


}

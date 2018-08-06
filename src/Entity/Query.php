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

    const HOTELS = [
        self::HOTEL1 => "Hotel 1",
        self::HOTEL2 => "Hotel 2",
        self::HOTEL3 => "Hotel 3",
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
     * @var bool
     *
     * @ORM\Column(name="approved", type="boolean", options={"default": 0})
     */
    private $approved = 0;

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
    public function setDateOfArrival(\DateTime $dateOfArrival): void
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
    public function setDateOfDeparture(\DateTime $dateOfDeparture): void
    {
        $this->dateOfDeparture = $dateOfDeparture;
    }

    /**
     * @return bool
     */
    public function isApproved()
    {
        return $this->approved;
    }

    /**
     * @param bool $approved
     */
    public function setApproved(bool $approved)
    {
        $this->approved = $approved;
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


}

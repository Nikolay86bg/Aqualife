<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="account")
 * @ORM\Entity(repositoryClass="App\Repository\AccountRepository")
 */
class Account
{
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
     * @ORM\Column(type="string", length=255, nullable=true)
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
        return $this->schedules;
    }

    /**
     * @param Schedule[] $schedules
     */
    public function setSchedules(array $schedules)
    {
        $this->schedules = $schedules;
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
                }
            }
        }

        return $return;
    }


}

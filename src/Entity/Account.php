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
     * @var Query[]
     *
     * * @ORM\OneToMany(targetEntity="App\Entity\Query", mappedBy="account")
     */
    private $queries;

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
     * @return Query[]
     */
    public function getQueries()
    {
        return $this->queries;
    }

    /**
     * @param Query[] $queries
     */
    public function setQueries(array $queries)
    {
        $this->queries = $queries;
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

    public function isSetPool()
    {
        $return = [];
        if($schedules = $this->getSchedules()){
            /** @var Schedule $schedule */
            foreach($schedules as $schedule){
                if($schedule->getFacility()->getType() == Facility::TYPE_POOL){
                    $return[$schedule->getId()]['date'] = $schedule->getDate();
                    $return[$schedule->getId()]['lanes'] = $schedule->getParts();
                }
            }
        }

        return $return;
    }



}

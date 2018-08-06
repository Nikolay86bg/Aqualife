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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSport(): ?string
    {
        return $this->sport;
    }

    public function setSport(?string $sport): self
    {
        $this->sport = $sport;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
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
    public function setManager($manager): void
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




}

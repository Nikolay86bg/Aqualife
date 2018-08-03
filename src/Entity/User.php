<?php

/*
 * (c) 411 Marketing
 */

namespace App\Entity;

use App\Doctrine\Traits\TimestampableEntityTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User.
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 *
 * @UniqueEntity("username")
 */
class User implements AdvancedUserInterface, \Serializable, EquatableInterface
{
    use TimestampableEntityTrait;

    const ROLE_USER = 'ROLE_USER';
//    const ROLE_TL = 'ROLE_TL';
//    const ROLE_SUPERVISOR = 'ROLE_SUPERVISOR';
    const ROLE_MANAGER = 'ROLE_MANAGER';
//    const ROLE_OPERATIONS_MANAGER = 'ROLE_OPERATIONS_MANAGER';
//    const ROLE_DIRECTOR = 'ROLE_DIRECTOR';
    const ROLE_ADMIN = 'ROLE_ADMIN';


    /**
     * Managers can edit and add users from this departments
     * Key is managers department
     * Values are array of departments they have permissions.
     */
//    const MANAGERS_DEPARTMENT_PERMISSIONS = [
//        'Advertising' => [
//            'Advertising',
//            'Link Building',
//            'Training Department',
//            'Web Design',
//            'Custom Web',
//            'Content Writing',
//            'Technical Support',
//        ],
//        'Link Building' => [
//            'Link Building',
//        ],
//        'Training Department' => [
//            'Training Department',
//        ],
//        'Web Design' => [
//            'Web Design',
//            'Custom Web',
//        ],
//        'Custom Web' => [
//            'Web Design',
//            'Custom Web',
//        ],
//        'Quality Assurance' => [
//            'Quality Assurance',
//        ],
//        'Content Writing' => [
//            'Content Writing',
//        ],
//        'Technical Support' => [
//            'Technical Support',
//        ],
//    ];

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255, unique=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string
     */
    private $plainPassword;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

//    /**
//     * @var User
//     *
//     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="children")
//     */
//    private $parent;

//    /**
//     * @var User[]
//     *
//     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="parent")
//     */
//    private $children;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255)
     */
    private $lastName;

    /**
     * @var array
     *
     * @ORM\Column(name="roles", type="array")
     */
    private $roles;

    /**
     * @var string
     *
     * @ORM\Column(name="position", type="string", length=255)
     */
    private $position;


    /**
     * @var Query[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Query", mappedBy="createdBy")
     */
    private $queries;

    /**
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param string $position
     */
    public function setPosition(string $position): void
    {
        $this->position = $position;
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





//    /**
//     * @var Department
//     *
//     * @ORM\ManyToOne(targetEntity="App\Entity\Department", inversedBy="users");
//     */
//    private $department;

//    /**
//     * @var Position
//     *
//     * @ORM\ManyToOne(targetEntity="App\Entity\Position", inversedBy="users");
//     */
//    private $position;

//    /**
//     * @var Mistake[]
//     *
//     * @ORM\OneToMany(targetEntity="App\Entity\Mistake", mappedBy="createdBy")
//     */
//    private $mistakes;
//
//    /**
//     * @var EventMistakeWeight[]
//     *
//     * @ORM\OneToMany(targetEntity="App\Entity\EventMistakeWeight", mappedBy="createdBy")
//     */
//    private $eventMistakeWeights;
//
//    /**
//     * @var Account[]
//     *
//     * @ORM\OneToMany(targetEntity="App\Entity\Account", mappedBy="createdBy")
//     */
//    private $accounts;
//
//    /**
//     * @var Evaluation[]
//     *
//     * @ORM\OneToMany(targetEntity="App\Entity\Evaluation", mappedBy="createdBy")
//     */
//    private $evaluations;
//
//    /**
//     * @var Evaluation[]
//     *
//     * @ORM\OneToMany(targetEntity="App\Entity\EvaluationEventMistakeWeight", mappedBy="createdBy")
//     */
//    private $evaluationEventMistakeWeights;
//
//    /**
//     * @var AccountEvent[]
//     *
//     * @ORM\OneToMany(targetEntity="App\Entity\AccountEvent", mappedBy="createdBy")
//     */
//    private $accountEventsCreated;
//
//    /**
//     * @var AccountEvent[]
//     *
//     * @ORM\OneToMany(targetEntity="App\Entity\AccountEvent", mappedBy="assignedTo")
//     */
//    private $accountEventsAssigned;
//
//    /**
//     * @var AccountEvent[]
//     *
//     * @ORM\OneToMany(targetEntity="App\Entity\AccountEvent", mappedBy="closedBy")
//     */
//    private $accountEventsClosed;
//
//    /**
//     * @var Dispute[]
//     *
//     * @ORM\OneToMany(targetEntity="App\Entity\Dispute", mappedBy="createdBy")
//     */
//    private $disputes;
//
//    /**
//     * @var Comment[]
//     *
//     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="createdBy")
//     */
//    private $comments;

//    /**
//     * @var string
//     *
//     * @ORM\Column(name="timezone", type="string", length=255, options={"default": "Europe/Sofia"})
//     */
//    private $timezone;

//    /**
//     * @var DisputeEvaluationEventMistakeWeight[]
//     *
//     * @ORM\OneToMany(targetEntity="App\Entity\DisputeEvaluationEventMistakeWeight", mappedBy="createdBy")
//     */
//    private $disputeEvaluationEventMistakeWeights;
//
//    /**
//     * @var AccountEventWorkTime[]
//     *
//     * @ORM\OneToMany(targetEntity="App\Entity\AccountEventWorkTime", mappedBy="user")
//     */
//    private $accountEventWorkTimes;
//
//    /**
//     * @var ParameterType[]
//     *
//     * @ORM\OneToMany(targetEntity="App\Entity\ParameterType", mappedBy="createdBy")
//     */
//    private $parameterTypes;
//
//    /**
//     * @var Calendar[]
//     *
//     * @ORM\OneToMany(targetEntity="App\Entity\Calendar", mappedBy="createdBy")
//     */
//    private $holidays;
//
//    /**
//     * @var QnqReport[]
//     *
//     * @ORM\OneToMany(targetEntity="App\Entity\QnqReport", mappedBy="user")
//     */
//    private $qnqReports;
//
//    /**
//     * @var Account[]
//     *
//     * @ORM\OneToMany(targetEntity="App\Entity\Account", mappedBy="user")
//     */
//    private $account;
//
//    /**
//     * Many Tasks have Many Employees.
//     *
//     * @ORM\ManyToMany(targetEntity="App\Entity\Task", mappedBy="employees")
//     */
//    private $tasks;
//
//    /**
//     * @var AccountEventChange[]
//     *
//     * @ORM\OneToMany(targetEntity="AccountEventChange", mappedBy="createdBy")
//     */
//    private $accountEventChange;
//
//    /**
//     * @var \DateTime
//     *
//     * @ORM\Column(name="off_work_from", type="datetime", nullable=true)
//     */
//    private $offWorkFrom;
//
//    /**
//     * @var \DateTime
//     *
//     * @ORM\Column(name="off_work_until", type="datetime", nullable=true)
//     */
//    private $offWorkUntil;
//
//    /**
//     * @var ShowPasswordLog[]
//     *
//     * @ORM\OneToMany(targetEntity="App\Entity\ShowPasswordLog", mappedBy="createdBy")
//     */
//    private $showPasswordLog;
//
//    /**
//     * @var Task
//     *
//     * @ORM\OneToMany(targetEntity="App\Entity\Task", mappedBy="completedBy")
//     */
//    private $taskCompletedBy;
//
//    /**
//     * @return QnqReport[]
//     */
//    public function getQnqReports()
//    {
//        return $this->qnqReports;
//    }
//
//    /**
//     * @return Account[]
//     */
//    public function getAccount()
//    {
//        return $this->account;
//    }
//
//    /**
//     * @param Account[] $account
//     */
//    public function setAccount(array $account)
//    {
//        $this->account = $account;
//    }
//
//    /**
//     * @param QnqReport[] $qnqReports
//     */
//    public function setQnqReports(array $qnqReports)
//    {
//        $this->qnqReports = $qnqReports;
//    }

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->roles = [self::ROLE_USER];
//        $this->parameterTypes = new ArrayCollection();
//        $this->holidays = new ArrayCollection();
//        $this->tasks = new ArrayCollection();
//        $this->accountEventChange = new ArrayCollection();
        $this->showPasswordLog = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s %s', $this->getFirstName(), $this->getLastName());
    }

    /**
     * @return Evaluation[]
     */
    public function getEvaluationEventMistakeWeights()
    {
        return $this->evaluationEventMistakeWeights;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username.
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password.
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $plainPassword
     *
     * @return User
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
        $this->password = null;

        return $this;
    }

    /**
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * Set isActive.
     *
     * @param bool $isActive
     *
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive.
     *
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

//    /**
//     * Set parent.
//     *
//     * @param User $parent
//     *
//     * @return User
//     */
//    public function setParent($parent)
//    {
//        $this->parent = $parent;
//
//        return $this;
//    }
//
//    /**
//     * Get parent.
//     *
//     * @return User
//     */
//    public function getParent()
//    {
//        return $this->parent;
//    }

    /**
     * Set `irstName.
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName.
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName.
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName.
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->username,
            $this->password,
        ]);
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->username,
            $this->password) = unserialize($serialized);
    }

    /**
     * @return User
     */
    public function eraseCredentials()
    {
        $this->password = null;
        $this->plainPassword = null;

        return $this;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     *
     * @return $this
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    public function getSalt()
    {
        return null;
    }

    /**
     * @return bool
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isAccountNonLocked()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->isActive;
    }

    /**
     * @param UserInterface $user
     *
     * @return bool
     */
    public function isEqualTo(UserInterface $user)
    {
        return $this->getId() === $user->getId();
    }
//
//    /**
//     * @param User[] $children
//     *
//     * @return User
//     */
//    public function setChildren($children)
//    {
//        $this->children = $children;
//
//        return $this;
//    }
//
//    /**
//     * @return User[]
//     */
//    public function getChildren()
//    {
//        return $this->children;
//    }

//    /**
//     * @param Department $department
//     *
//     * @return User
//     */
//    public function setDepartment($department)
//    {
//        $this->department = $department;
//
//        return $this;
//    }
//
//    /**
//     * @return Department
//     */
//    public function getDepartment()
//    {
//        return $this->department;
//    }

//    /**
//     * @param Position $position
//     *
//     * @return User
//     */
//    public function setPosition($position)
//    {
//        $this->position = $position;
//
//        return $this;
//    }
//
//    /**
//     * @return Position
//     */
//    public function getPosition()
//    {
//        return $this->position;
//    }
//
//    /**
//     * @param Mistake[] $mistakes
//     *
//     * @return User
//     */
//    public function setMistakes($mistakes)
//    {
//        $this->mistakes = $mistakes;
//
//        return $this;
//    }
//
//    /**
//     * @return Mistake[]
//     */
//    public function getMistakes()
//    {
//        return $this->mistakes;
//    }
//
//    /**
//     * @param EventMistakeWeight[] $eventMistakeWeights
//     *
//     * @return User
//     */
//    public function setEventMistakeWeights($eventMistakeWeights)
//    {
//        $this->eventMistakeWeights = $eventMistakeWeights;
//
//        return $this;
//    }
//
//    /**
//     * @return EventMistakeWeight[]
//     */
//    public function getEventMistakeWeights()
//    {
//        return $this->eventMistakeWeights;
//    }
//
//    /**
//     * @param Account[] $accounts
//     *
//     * @return User
//     */
//    public function setAccounts($accounts)
//    {
//        $this->accounts = $accounts;
//
//        return $this;
//    }
//
//    /**
//     * @return Account[]
//     */
//    public function getAccounts()
//    {
//        return $this->accounts;
//    }
//
//    /**
//     * @param AccountEvent[] $accountEventsCreated
//     *
//     * @return User
//     */
//    public function setAccountEventsCreated($accountEventsCreated)
//    {
//        $this->accountEventsCreated = $accountEventsCreated;
//
//        return $this;
//    }
//
//    /**
//     * @return AccountEvent[]
//     */
//    public function getAccountEventsCreated()
//    {
//        return $this->accountEventsCreated;
//    }
//
//    /**
//     * @param AccountEvent[] $accountEventsAssigned
//     *
//     * @return User
//     */
//    public function setAccountEventsAssigned($accountEventsAssigned)
//    {
//        $this->accountEventsAssigned = $accountEventsAssigned;
//
//        return $this;
//    }
//
//    /**
//     * @return AccountEvent[]
//     */
//    public function getAccountEventsAssigned()
//    {
//        return $this->accountEventsAssigned;
//    }
//
//    /**
//     * @param AccountEvent[] $accountEventsClosed
//     *
//     * @return User
//     */
//    public function setAccountEventsClosed($accountEventsClosed)
//    {
//        $this->accountEventsClosed = $accountEventsClosed;
//
//        return $this;
//    }
//
//    /**
//     * @return AccountEvent[]
//     */
//    public function getAccountEventsClosed()
//    {
//        return $this->accountEventsClosed;
//    }
//
//    /**
//     * @param $evaluations
//     *
//     * @return User
//     */
//    public function setEvaluations($evaluations)
//    {
//        $this->evaluations = $evaluations;
//
//        return $this;
//    }
//
//    /**
//     * @return Evaluation[]
//     */
//    public function getEvaluations()
//    {
//        return $this->evaluations;
//    }
//
//    /**
//     * @param Dispute[] $disputes
//     *
//     * @return User
//     */
//    public function setDisputes($disputes)
//    {
//        $this->disputes = $disputes;
//
//        return $this;
//    }
//
//    /**
//     * @return Dispute[]
//     */
//    public function getDisputes()
//    {
//        return $this->disputes;
//    }
//
//    /**
//     * @param Comment[] $comments
//     *
//     * @return User
//     */
//    public function setComments($comments)
//    {
//        $this->comments = $comments;
//
//        return $this;
//    }
//
//    /**
//     * @return Comment[]
//     */
//    public function getComments()
//    {
//        return $this->comments;
//    }
//
//    /**
//     * @param string $timezone
//     *
//     * @return User
//     */
//    public function setTimezone($timezone)
//    {
//        $this->timezone = $timezone;
//
//        return $this;
//    }
//
//    /**
//     * @return string
//     */
//    public function getTimezone()
//    {
//        return $this->timezone;
//    }

//    /**
//     * @param DisputeEvaluationEventMistakeWeight[] $disputeEvaluationEventMistakeWeights
//     *
//     * @return User
//     */
//    public function setDisputeEvaluationEventMistakeWeights($disputeEvaluationEventMistakeWeights)
//    {
//        $this->disputeEvaluationEventMistakeWeights = $disputeEvaluationEventMistakeWeights;
//
//        return $this;
//    }
//
//    /**
//     * @return DisputeEvaluationEventMistakeWeight[]
//     */
//    public function getDisputeEvaluationEventMistakeWeights()
//    {
//        return $this->disputeEvaluationEventMistakeWeights;
//    }
//
//    /**
//     * @param AccountEventWorkTime[] $accountEventWorkTimes
//     *
//     * @return User
//     */
//    public function setAccountEventWorkTimes($accountEventWorkTimes)
//    {
//        $this->accountEventWorkTimes = $accountEventWorkTimes;
//
//        return $this;
//    }
//
//    /**
//     * @return AccountEventWorkTime[]
//     */
//    public function getAccountEventWorkTimes()
//    {
//        return $this->accountEventWorkTimes;
//    }
//
//    /**
//     * @return ParameterType[]
//     */
//    public function getParameterTypes()
//    {
//        return $this->parameterTypes;
//    }
//
//    /**
//     * @return Holiday[]
//     */
//    public function getHolidays()
//    {
//        return $this->holidays;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getTasks()
//    {
//        return $this->tasks;
//    }
//
//    /**
//     * @return AccountEventChange[]
//     */
//    public function getAccountEventChange()
//    {
//        return $this->accountEventChange;
//    }

//    /**
//     * @return \DateTime
//     */
//    public function getOffWorkFrom()
//    {
//        return $this->offWorkFrom;
//    }
//
//    /**
//     * @param \DateTime $offWorkFrom
//     */
//    public function setOffWorkFrom($offWorkFrom)
//    {
//        $this->offWorkFrom = $offWorkFrom;
//    }
//
//    /**
//     * @return \DateTime
//     */
//    public function getOffWorkUntil()
//    {
//        return $this->offWorkUntil;
//    }
//
//    /**
//     * @param \DateTime $offWorkUntil
//     */
//    public function setOffWorkUntil($offWorkUntil)
//    {
//        $this->offWorkUntil = $offWorkUntil;
//    }

    /**
     * @return ShowPasswordLog[]
     */
    public function getShowPasswordLog()
    {
        return $this->showPasswordLog;
    }

    /**
     * @param ShowPasswordLog[] $showPasswordLog
     */
    public function setShowPasswordLog(array $showPasswordLog)
    {
        $this->showPasswordLog = $showPasswordLog;
    }

//    /**
//     * @return Task
//     */
//    public function getTaskCompletedBy()
//    {
//        return $this->taskCompletedBy;
//    }
//
//    /**
//     * @param Task $taskCompletedBy
//     */
//    public function setTaskCompletedBy($taskCompletedBy)
//    {
//        $this->taskCompletedBy = $taskCompletedBy;
//    }
}

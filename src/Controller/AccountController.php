<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\MealSchedule;
use App\Entity\Schedule;
use App\Form\AccountFilterType;
use App\Form\SchedulePasswordType;
use App\Form\AccountType;
use App\Service\ColorService;
use App\Service\MailerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Intl\Intl;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class AccountController
 * @package App\Controller
 */
class AccountController extends AbstractController
{
    /**
     * @var ColorService
     */
    private $colorService;
    /**
     * @var MailerService
     */
    private $mailerService;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(ColorService $colorService, MailerService $mailerService, TranslatorInterface $translator)
    {
        $this->colorService = $colorService;
        $this->mailerService = $mailerService;
        $this->translator = $translator;
    }

    /**
     * @Route("/account", name="account")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {
//        $this->denyAccessUnlessGranted(UserVoter::USER_LIST_ROLE);

        $filter = $this->createForm(AccountFilterType::class);
        $filter->handleRequest($request);

        $entityManager = $this->getDoctrine()->getManager();
        $sort = null;
        $order = null;

        if (!empty($request->get('sort')) && !empty($request->get('order'))) {
            $sort = $request->get('sort');
            $order = $request->get('order');
        }

        $accounts = $entityManager->getRepository(Account::class)->getListQuery($filter, $sort, $order);

        $accounts = (new Paginator($accounts))
            ->setEntityManager($this->getDoctrine()->getManager())
            ->paginate($request->query->get('page'));

        return $this->render('account/index.html.twig', [
            'filter' => $filter->createView(),
            'accounts' => $accounts,
            'countries' => Intl::getRegionBundle()->getCountryNames(),
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response.
     */
    public function new(Request $request)
    {
//        $this->denyAccessUnlessGranted(UserVoter::USER_ADD_ROLE);
        $account = new Account();
        $form = $this->createForm(AccountType::class, $account);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($account);
            $em->flush();

            return $this->redirectToRoute('account_show', ['id' => $account->getId()]);
        }

        return $this->render('account/new.html.twig', [
            'account' => $account,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Account $account
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(Account $account)
    {
//        $this->denyAccessUnlessGranted(UserVoter::USER_VIEW_ROLE, $user);

        $countries = Intl::getRegionBundle()->getCountryNames();

        return $this->render('account/show.html.twig', [
            'account' => $account,
            'countries' => $countries,
        ]);
    }

    public function edit(Request $request, Account $account)
    {
//        $this->denyAccessUnlessGranted(UserVoter::USER_EDIT_ROLE, $user);

        $editForm = $this->createForm(AccountType::class, $account);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->flush();

            return $this->redirectToRoute('account_show', ['id' => $account->getId()]);
        }

        return $this->render('account/edit.html.twig', [
            'account' => $account,
            'edit_form' => $editForm->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function teamsSchedule(Request $request)
    {
        $form = $this->createForm(SchedulePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $activeAccounts = [];
            foreach($this->getDoctrine()->getManager()->getRepository(Account::class)->getCurrentAccounts() as $account){
                $activeAccounts[$account->getQuery()->getId()] = $account->getId();
            }

            if (array_key_exists($form->get('password')->getData(),$activeAccounts)) {
                return $this->redirectToRoute('account_schedule', ['id' =>$activeAccounts[$form->get('password')->getData()]]);
            } else {
                $this->addFlash('error', 'Wrong Password!');
            }
        }

        return $this->render('account/password-check.html.twig', [
            'form' => $form->createView(),
        ]);

//        return $this->render('account/current-accounts-list.html.twig', [
//            'accounts' => $this->getDoctrine()->getManager()->getRepository(Account::class)->getCurrentAccounts(),
//            'color' => $this->colorService
//        ]);
    }

    /**
     * @param Account $account
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function schedule(Account $account)
    {
        $scheduleArray = $mealArray = [];
        if ($schedules = $this->getDoctrine()->getManager()->getRepository(Schedule::class)->findBy([
            'account' => $account,
            'deleted' => null,
        ], [
            'date' => 'ASC',
            'timeFrom' => 'ASC',
        ])
        ) {
            foreach ($schedules as $schedule) {
                $scheduleArray[$schedule->getDate()->format('d-m-Y')][] = $schedule;
            }
        }

        if ($meals = $this->getDoctrine()->getManager()->getRepository(MealSchedule::class)->findBy([
            'account' => $account,
            'deleted' => null,
        ], [
            'date' => 'ASC'
        ])
        ) {
            foreach ($meals as $meal) {
                $mealArray[$meal->getDate()->format('d-m-Y')][] = $meal;
            }
        }

        ksort($scheduleArray);
        ksort($mealArray);

        return $this->render('account/schedule.html.twig', [
            'account' => $account,
            'color' => $this->colorService,
            'countries' => Intl::getRegionBundle()->getCountryNames(),
            'mealArray' => $mealArray,
            'scheduleArray' => $scheduleArray,
        ]);
    }

    /**
     * @param Account $account
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Account $account)
    {
        $account->setDeletedAt((new \DateTime()));
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', $this->translator->trans('general.flashes.deleted'));

        return $this->redirectToRoute('account_index');
    }
}

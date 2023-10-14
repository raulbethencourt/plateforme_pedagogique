<?php
/**
 * This file controlls user interactions
 * 
 * PHP version 8.2
 * 
 * @category Controller
 * @package  Plataform
 * @author   Raul Bethencourt Gonzalez <raul.bethencourt.pro@gmail.com> 
 * @license  GNU General Public License v3.0
 * @link     https://github.com/raulbethencourt/plateforme_pedagogique
 */
namespace App\Controller;

use App\Controller\Service\InvitationsController;
use App\Entity\Invite;
use App\Entity\Questionnaire;
use App\Form\EditUserType;
use App\Form\InviteType;
use App\Form\SearchUserType;
use App\Service\BreadCrumbsService as BreadCrumbs;
use App\Service\FindEntity;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * This class manage all users interface
 * 
 * @category Controller
 * @package  Plataform
 * @author   Raul Bethencourt Gonzalez <raul.bethencourt.pro@gmail.com> 
 * @license  GNU General Public License v3.0
 * @link     https://github.com/raulbethencourt/plateforme_pedagogique
 */
class UserController extends AbstractController
{
    private $_em;
    private $_find;
    private $_request;
    private $_breadCrumbs;
    private $_paginator;
    private $_doctrine;

    /**
     * Function construct
     * 
     * @param EntityManagerInterface $_em          to set Entities
     * @param FindEntity             $_find        to find entities
     * @param RequestStack           $requestStack the request info
     * @param BreadCrumbs            $_breadCrumbs show me the way back
     * @param PaginatorInterface     $_paginator   order my data
     * @param ManagerRegistry        $_doctrine    manage the data input
     */
    public function __construct(
        EntityManagerInterface $_em,
        FindEntity $_find,
        RequestStack $requestStack,
        BreadCrumbs $_breadCrumbs,
        PaginatorInterface $_paginator,
        ManagerRegistry $_doctrine
    ) {
        $this->_em = $_em;
        $this->_find = $_find;
        $this->_request = $requestStack->getCurrentRequest();
        $this->_breadCrumbs = $_breadCrumbs;
        $this->_paginator = $_paginator;
        $this->_doctrine = $_doctrine;
    }

    /**
     * Shows users home pages
     * 
     * @param InvitationsController $invitation give acces to the plataform
     * 
     * @return Response
     */
    #[Route('/user', name: 'user_show', methods: ['GET'])]
    #[Route('/teacher', name: 'teacher_show', methods: ['GET'])]
    #[Route('/student', name: 'student_show', methods: ['GET'])]
    public function show(InvitationsController $invitation): Response
    {
        $user = $this->getUser();
        $user_role = $user->getRoles()[0];

        $classrooms = match ($user_role) {
            'ROLE_ADMIN', 'ROLE_TEACHER' => $user->getClassrooms(),
            'ROLE_STUDENT' => $user->getClassrooms()[0],
            default => $this->_find->findAllClassrooms(),
        };

        // admin invitation
        if ('ROLE_ADMIN' == $user_role) {
            $invite = new Invite();
            $form = $this->createForm(InviteType::class, $invite, ['user' => $user]);
            $invitation->invitation($form, $invite);
        }

        $users_data = match ($user_role) {
            'ROLE_TEACHER' => [
                'teacher' => $user,
            ],
            'ROLE_STUDENT' => [
                'student' => $user,
                'lessons' => $this->_paginator->paginate(
                    $classrooms->getLessons(),
                    $this->_request->query->getInt('page', 1),
                    10
                ),
            ],
            default => [
                'admins' => $this->_find->findUsersByRole('ROLE_ADMIN'),
                'form' => $form->createView(),
            ],
        };

        $render_data = array_merge(
            $users_data, [
                'classrooms' => $this->_paginator->paginate(
                    $classrooms,
                    $this->_request->query->getInt('page', 1),
                    10
                ), 
            ]
        );

        return $this->render('user/show.html.twig', $render_data);
    }

    /**
     * Show list of users
     *
     * @return Response
     */
    #[Route(
        '/user/list',
        name: 'user_list',
        methods: ['GET']
    )]
    public function listUsers(): Response
    {
        $type = $this->_request->query->get('type');
        $listProfileEdit = $this->_request->query->get('list_profile_edit');

        $form = $this->createForm(SearchUserType::class);
        $form->handleRequest($this->_request);
        if ($form->isSubmitted() && $form->isValid()) {
            $name = $form->getData()['name'];
            $surname = $form->getData()['surname'];
            $email = $form->getData()['email'];
            $phone = $form->getData()['telephone'];

            $users = $this->_find->searchUser($name, $surname, $email, $phone);
            $users = $this->_paginator->paginate(
                $users, 
                $this->_request->query->getInt('page', 1), 
                10
            );

            return $this->render(
                'user/list.html.twig', [
                    'users' => $users,
                    'type' => $type,
                    'list_profile_edit' => $listProfileEdit,
                    'form' => $form->createView(),
                ]
            );
        }

        if ('teachers' === $type) {
            $users = $this->_find->findUsersByRole('ROLE_TEACHER');
            $this->_breadCrumbs->bcListUsers($type, null);
        } else {
            $users = $this->_find->findUsersByRole('ROLE_STUDENT');
            $this->_breadCrumbs->bcListUsers($type, null);
        }

        $users = $this->_paginator->paginate(
            $users, 
            $this->_request->query->getInt('page', 1), 
            10
        );

        return $this->render(
            'user/list.html.twig', [
                'users' => $users,
                'type' => $type,
                'list_profile_edit' => $listProfileEdit,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Delete a user from application
     *
     * @return RedirectResponse
     */
    #[Route(
        '/user/{id}',
        name: 'user_delete',
        methods: ['POST']
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteUser(): RedirectResponse
    {
        $user = $this->_find->findUser();
        $role = $user->getRoles()[0];
        // Check the token
        if ($this->isCsrfTokenValid(
            'delete'.$user->getId(),
            $this->_request->get('_token')
        )
        ) {
            $this->_em->remove($user);
            $this->_em->flush();
            if ('ROLE_TEACHER' === $role || 'ROLE_STUDENT' === $role) {
                $this->addFlash(
                    'success', 
                    'Utilisateur·rice supprimé·e avec succès.'
                );
            } else {
                $this->addFlash(
                    'success', 
                    'Administrateur·rice supprimé·e avec succès.'
                );
            }
        }

        if ('ROLE_TEACHER' === $role || 'ROLE_STUDENT' === $role) {
            if ('ROLE_TEACHER' === $role) {
                return $this->redirectToRoute(
                    'user_list', [
                    'type' => 'teachers',
                    ]
                );
            }

            return $this->redirectToRoute(
                'user_list', [
                'type' => 'students',
                ]
            );
        }

        return $this->redirectToRoute('user_show');
    }

    /**
     * Show user profile
     *
     * @return Response
     */
    #[Route(
        '/user/profile',
        name: 'user_profile',
        methods: ['GET']
    )]
    #[Route(
        '/teacher/profile',
        name: 'teacher_profile',
        methods: ['GET']
    )]
    #[Route(
        '/student/profile',
        name: 'student_profile',
        methods: ['GET']
    )]
    public function profile(): Response
    {
        $user = $this->getUser();
        $user_role = $user->getRoles()[0];

        $this->_breadCrumbs->bcProfile(false, false);

        // student data for charts
        if ('ROLE_STUDENT' == $user_role) {
            // Get each time that the student has passed q questionnaire
            $passes = $this->_find->findPasses($this->getUser());

            $sum = array_reduce(
                $passes,
                function ($i, $pass) {
                    return $i += $pass->getPoints();
                }
            );

            $numberOfQuestions = array_reduce(
                $passes,
                function ($i, $pass) {
                    return $i += count($pass->getQuestionnaire()->getQuestions());
                }
            );

            $difficulties = Questionnaire::DIFFICULTIES;
            $playsPerDiff = [];
            $statsPerDiff = [];

            foreach ($difficulties as $difficulty) {
                $playsPerDiff[$difficulty] = array_filter(
                    $passes,
                    function ($pass) use ($difficulty) {
                        return $pass->getQuestionnaire()
                            ->getDifficulty() == $difficulty;
                    }
                );

                $totalScore = array_reduce(
                    $playsPerDiff[$difficulty],
                    function ($i, $play) {
                        return $i += $play->getQuestionnaire()->getTotalScore();
                    }
                );

                $playerScore = array_reduce(
                    $playsPerDiff[$difficulty],
                    function ($i, $play) {
                        return $i += $play->getPoints();
                    }
                );

                if (null != $totalScore) {
                    $statsPerDiff[$difficulty] = round(
                        ($playerScore / $totalScore) * 100, 
                        2
                    );
                } else {
                    $statsPerDiff[$difficulty] = null;
                }
            }

            $sumMax = array_reduce(
                $passes,
                function ($i, $pass) {
                    return $i += $pass->getQuestionnaire()->getTotalScore();
                }
            );

            if ($sumMax) {
                $average = (round($sum / $sumMax, 2) * 100).'%';
            } else {
                $average = 0;
            }
        }

        $render_data = match ($user_role) {
            'ROLE_TEACHER' => [
                'teacher' => $user,
            ],
            'ROLE_STUDENT' => [
                'student' => $user,
                'passes' => $passes,
                'sum' => $sum,
                'average' => $average,
                'statsPerDiff' => $statsPerDiff,
                'spdjson' => json_encode(array_values($statsPerDiff)),
                'numberOfQuestions' => $numberOfQuestions,
                'avatar' => $user->getAvatar(),
            ],
            default => [
                'user' => $user,
            ],
        };

        return $this->render('user/profile.html.twig', $render_data);
    }

    /**
     * Edit user profile
     *
     * @return Response
     */
    #[Route(
        '/user/profile/edit',
        name: 'user_edit_profile',
        methods: ['GET', 'POST']
    )]
    #[Route(
        '/teacher/profile/edit',
        name: 'teacher_edit_profile',
        methods: ['GET', 'POST']
    )]
    #[Route(
        '/student/profile/edit',
        name: 'student_edit_profile',
        methods: ['GET', 'POST']
    )]
    public function editProfile(): Response
    {
        if ($this->_request->query->get('list_profile_edit')) {
            $this->_breadCrumbs->bcListUsers(
                'students', 
                $this->_request->query->get('list_profile_edit')
            );
        } else {
            $this->_breadCrumbs->bcProfile(true);
        }
        
        $user_name = $this->_request->query->get('username');

        $user = (isset($user_name)) ?
            $this->_find->findStudentByUsername($user_name) :
            $this->getUser();
        
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($this->_request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->_doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Profil édité avec succès.');
            
            if (isset($user_name)) {
                return $this->redirectToRoute(
                    'user_list', [
                        'type' => 'students',
                    ]
                );
            }

            return $this->redirectToRoute('user_profile');
        }

        return $this->render(
            'user/edit-profile.html.twig', [
                'editForm' => $form->createView(),
            ]
        );
    }
}

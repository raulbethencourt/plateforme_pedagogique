<?php

namespace App\Controller;

use App\Entity\Questionnaire;
use App\Form\EditStudentType;
use App\Controller\Service\InvitationsController;
use App\Entity\Invite;
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

class UserController extends AbstractController
{
    private $em;
    private $find;
    private $request;
    private $breadCrumbs;
    private $paginator;
    private $doctrine;

    public function __construct(
        EntityManagerInterface $em,
        FindEntity $find,
        RequestStack $requestStack,
        BreadCrumbs $breadCrumbs,
        PaginatorInterface $paginator,
        ManagerRegistry $doctrine
    ) {
        $this->em = $em;
        $this->find = $find;
        $this->request = $requestStack->getCurrentRequest();
        $this->breadCrumbs = $breadCrumbs;
        $this->paginator = $paginator;
        $this->doctrine = $doctrine;
    }

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
            default => $this->find->findAllClassrooms(),
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
                'lessons' => $this->paginator->paginate(
                    $classrooms->getLessons(),
                    $this->request->query->getInt('page', 1),
                    10
                ),
            ],
            default => [
                'admins' => $this->find->findUsersByRole('ROLE_ADMIN'),
                'form' => $form->createView(),
            ],
        };

        $render_data = array_merge($users_data, [
            'classrooms' => $this->paginator->paginate(
                $classrooms,
                $this->request->query->getInt('page', 1),
                10
            ),
        ]);

        return $this->render('user/show.html.twig', $render_data);
    }

    #[Route(
        '/user/list',
        name: 'user_list',
        methods: ['GET']
    )]
    public function listUsers(): Response
    {
        $type = $this->request->query->get('type');
        $listProfileEdit = $this->request->query->get('list_profile_edit');

        $form = $this->createForm(SearchUserType::class);
        $form->handleRequest($this->request);
        if ($form->isSubmitted() && $form->isValid()) {
            $name = $form->getData()['name'];
            $surname = $form->getData()['surname'];
            $email = $form->getData()['email'];
            $phone = $form->getData()['telephone'];

            $users = $this->find->searchUser($name, $surname, $email, $phone);
            $users = $this->paginator->paginate($users, $this->request->query->getInt('page', 1), 10);

            return $this->render('user/list.html.twig', [
                'users' => $users,
                'type' => $type,
                'list_profile_edit' => $listProfileEdit,
                'form' => $form->createView(),
            ]);
        }

        if ('teachers' === $type) {
            $users = $this->find->findUsersByRole('ROLE_TEACHER');
            $this->breadCrumbs->bcListUsers($type, null);
        } else {
            $users = $this->find->findUsersByRole('ROLE_STUDENT');
            $this->breadCrumbs->bcListUsers($type, null);
        }

        $users = $this->paginator->paginate($users, $this->request->query->getInt('page', 1), 10);

        return $this->render('user/list.html.twig', [
            'users' => $users,
            'type' => $type,
            'list_profile_edit' => $listProfileEdit,
            'form' => $form->createView(),
        ]);
    }

    #[Route(
        '/user/{id}',
        name: 'user_delete',
        methods: ['POST']
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteUser(): RedirectResponse
    {
        $user = $this->find->findUser();
        $role = $user->getRoles()[0];
        // Check the token
        if ($this->isCsrfTokenValid(
            'delete'.$user->getId(),
            $this->request->get('_token')
        )) {
            $this->em->remove($user);
            $this->em->flush();
            if ('ROLE_TEACHER' === $role || 'ROLE_STUDENT' === $role) {
                $this->addFlash('success', 'Utilisateur·rice supprimé·e avec succès.');
            } else {
                $this->addFlash('success', 'Administrateur·rice supprimé·e avec succès.');
            }
        }

        if ('ROLE_TEACHER' === $role || 'ROLE_STUDENT' === $role) {
            if ('ROLE_TEACHER' === $role) {
                return $this->redirectToRoute('user_list', [
                    'type' => 'teachers',
                ]);
            }

            return $this->redirectToRoute('user_list', [
                'type' => 'students',
            ]);
        }

        return $this->redirectToRoute('user_show');
    }

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

        $this->breadCrumbs->bcProfile(false, false);

        // student data for charts 
        if ('ROLE_STUDENT' == $user_role) {
            // Get each time that the student has passed q questionnaire
            $passes = $this->find->findPasses($this->getUser());

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
                        return $pass->getQuestionnaire()->getDifficulty() == $difficulty;
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
                    $statsPerDiff[$difficulty] = round(($playerScore / $totalScore) * 100, 2);
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

    #[Route(
        '/user/profile/edit',
        name: 'user_edit_profile',
        methods: ['GET', 'POST']
    )]
    public function editProfile(): Response
    {
        // TODO: continue refactoring
        $this->breadCrumbs->bcProfile(true, false);

        $user = $this->getUser();
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Profil édité avec succès.');

            return $this->redirectToRoute('user_profile');
        }

        return $this->render('user/edit-profile.html.twig', [
            'editForm' => $form->createView(),
        ]);
    }
}

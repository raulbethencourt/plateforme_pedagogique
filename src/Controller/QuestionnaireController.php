<?php

namespace App\Controller;

use App\Entity\Pass;
use App\Entity\Questionnaire;
use App\Form\QuestionnaireType;
use App\Repository\QuestionnaireRepository;
use App\Service\BreadCrumbsService as BreadCrumbs;
use App\Service\FindEntity;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs as ModelBreadcrumbs;

/**
 * @Route("/questionnaire")
 */
class QuestionnaireController extends AbstractController
{
    private $em;

    private $find;

    private $request;

    private $breadCrumbs;

    public function __construct(EntityManagerInterface $em, FindEntity $find, RequestStack $request, BreadCrumbs $breadCrumbs)
    {
        $this->em = $em;
        $this->find = $find;
        $this->request = $request->getCurrentRequest();
        $this->breadCrumbs = $breadCrumbs;
    }

    /**
     * @Route("/", name="questionnaire_index", methods={"GET"})
     * @Security("is_granted('ROLE_TEACHER') or is_granted('ROLE_ADMIN')")
     */
    public function index(QuestionnaireRepository $questionnaireRepo, PaginatorInterface $paginator): Response
    {
        $this->questionnaireBC(null, 'index');

        $user = $this->getUser();
        if ('ROLE_TEACHER' === $user->getRoles()[0]) {
            $questionnaires = $questionnaireRepo->findByVisibilityOrCreator(true, $user->getUsername());
        } else {
            $questionnaires = $questionnaireRepo->findAll();
        }

        $questionnaires = $paginator->paginate(
            $questionnaires,
            $this->request->query->getInt('page', 1),
            10
        );
        $questionnaires->setCustomParameters([
            'align' => 'center',
            'rounded' => true,
        ]);

        return $this->render('questionnaire/index.html.twig', [
            'questionnaires' => $questionnaires,
            'lesson_id' => $this->request->query->get('lesson_id'),
            'classroom_id' => $this->request->query->get('classroom_id'),
            'list' => $this->request->query->get('list'),
            'lonely' => $this->request->query->get('lonely'),
            'extra' => $this->request->query->get('extra'),
        ]);
    }

    /**
     * @Route("/new", name="questionnaire_new", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_TEACHER') or is_granted('ROLE_ADMIN')")
     */
    public function new(Request $request): Response
    {
        $this->questionnaireBC(null, 'new');

        $lesson = $this->find->findLesson();
        $questionnaire = new Questionnaire();

        if (isset($lesson)) {
            $questionnaire->addLesson($lesson);
        }

        $questionnaire->setDateCreation(new \DateTime());
        $questionnaire->setCreator($this->getUser()->getUsername());
        $form = $this->createForm(QuestionnaireType::class, $questionnaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($questionnaire);
            $this->em->flush();

            $this->addFlash('success', 'Activité ajouté avec succès.');

            return $this->redirectToRoute('question_new', [
                'questionnaire_id' => $questionnaire->getId(),
                'lesson_id' => $this->request->query->get('lesson_id'),
                'classroom_id' => $this->request->query->get('classroom_id'),
                'list' => $this->request->query->get('list'),
                'lonely' => $this->request->query->get('lonely'),
                'extra' => $this->request->query->get('extra'),
            ]);
        }

        return $this->render('questionnaire/new.html.twig', [
            'questionnaire' => $questionnaire,
            'form' => $form->createView(),
            'user' => $this->getUser(),
            'lesson_id' => $this->request->query->get('lesson_id'),
            'classroom_id' => $this->request->query->get('classroom_id'),
            'list' => $this->request->query->get('list'),
            'lonely' => $this->request->query->get('lonely'),
            'extra' => $this->request->query->get('extra'),
        ]);
    }

    /**
     * @Route("/{id}", name="questionnaire_show", methods={"GET"})
     * @Security("is_granted('ROLE_TEACHER') or is_granted('ROLE_ADMIN')")
     */
    public function show(Questionnaire $questionnaire): Response
    {
        $this->questionnaireBC($questionnaire, 'show');

        return $this->render('questionnaire/show.html.twig', [
            'questionnaire' => $questionnaire,
            'questions' => $questionnaire->getQuestions(),
            'lesson_id' => $this->request->query->get('lesson_id'),
            'classroom_id' => $this->request->query->get('classroom_id'),
            'list' => $this->request->query->get('list'),
            'lonely' => $this->request->query->get('lonely'),
            'extra' => $this->request->query->get('extra'),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="questionnaire_edit", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_TEACHER') or is_granted('ROLE_ADMIN')")
     */
    public function edit(Questionnaire $questionnaire, Request $request): Response
    {
        $this->questionnaireBC($questionnaire, 'edit');

        $form = $this->createForm(QuestionnaireType::class, $questionnaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($questionnaire);
            $this->em->flush();

            $this->addFlash('success', 'Activite modifié avec succès.');

            return $this->redirectToRoute(
                'questionnaire_show',
                [
                    'id' => $questionnaire->getId(),
                    'lesson_id' => $this->request->query->get('lesson_id'),
                    'classroom_id' => $this->request->query->get('classroom_id'),
                    'list' => $this->request->query->get('list'),
                    'lonely' => $this->request->query->get('lonely'),
                ]
            );
        }

        return $this->render(
            'questionnaire/edit.html.twig',
            [
                'questionnaire' => $questionnaire,
                'form' => $form->createView(),
                'user' => $this->getUser(),
                'lesson_id' => $this->request->query->get('lesson_id'),
                'classroom_id' => $this->request->query->get('classroom_id'),
                'list' => $this->request->query->get('list'),
                'lonely' => $this->request->query->get('lonely'),
            ]
        );
    }

    /**
     * @Route("/{id}", name="questionnaire_delete", methods={"DELETE"})
     * @Security("is_granted('ROLE_TEACHER') or is_granted('ROLE_ADMIN')")
     */
    public function delete(Questionnaire $questionnaire): Response
    {
        // check the token to delete
        if ($this->isCsrfTokenValid('delete'.$questionnaire->getId(), $this->request->query->get('_token'))) {
            $this->em->remove($questionnaire);
            $this->em->flush();
            $this->addFlash('success', 'Activité supprimé avec succès.');
        }

        return $this->redirectToRoute('questionnaire_index', [
            'lesson_id' => $this->request->query->get('lesson_id'),
            'classroom_id' => $this->request->query->get('classroom_id'),
            'list' => $this->request->query->get('list'),
            'lonely' => $this->request->query->get('lonely'),
        ]);
    }

    /**
     * @Route("/{id}/play", name="questionnaire_play", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_STUDENT') or is_granted('ROLE_TEACHER') or is_granted('ROLE_ADMIN')")
     */
    public function play(Questionnaire $questionnaire, Request $requestOne): Response
    {
        $this->questionnaireBC($questionnaire, 'play');

        // Check if we can play the questionnaire or not
        if (!$questionnaire->isPlayable()) {
            $this->addFlash('error', 'Activité indisponible !');
            $lesson_id = $this->request->query->get('lesson_id');

            return $this->redirectToRoute('questionnaire_show', [
                'id' => $questionnaire->getId(),
                'classroom_id' => $this->request->query->get('classroom_id'),
                'lesson_id' => $this->request->query->get('lesson_id'),
                'list' => $this->request->query->get('list'),
                'lonely' => $this->request->query->get('lonely'),
                'extra' => $this->request->query->get('extra'),
            ]);
        }

        // Creates the variables that I'm gonna need later on
        $answers = null;
        $rights = null;
        $points = null;

        if ($requestOne->isMethod('post')) {
            $answers = $requestOne->request; //equivalent à $_POST
            $eval = $this->evaluateQuestionnaire($answers, $questionnaire);
            $rights = $eval['corrects'];
            $points = $eval['points'];

            if ('ROLE_STUDENT' === $this->getUser()->getRoles()[0]) {
                $pass = $this->find->findPass($this->getUser(), $questionnaire);

                if (!$pass) {
                    $pass = new Pass();
                    $pass->setStudent($this->getUser());
                    $pass->setQuestionnaire($questionnaire);
                }

                $pass->setPoints($points);
                $pass->setDateRealisation(new \DateTime());
                $this->em->persist($pass);
                $this->em->flush();
            }
        }

        return $this->render('questionnaire/play.html.twig', [
            'questionnaire' => $questionnaire,
            'questions' => $questionnaire->getQuestions(),
            'points' => $points,
            'finalResults' => [
                'given' => $answers,
                'rights' => $rights,
            ],
            'user' => $this->getUser(),
            'lesson_id' => $this->request->query->get('lesson_id'),
            'classroom_id' => $this->request->query->get('classroom_id'),
            'list' => $this->request->query->get('list'),
            'lonely' => $this->request->query->get('lonely'),
            'extra' => $this->request->query->get('extra'),
        ]);
    }

    /**
     * This methode checks questionnaire answers.
     */
    private function evaluateQuestionnaire(ParameterBag $answers, Questionnaire $questionnaire): array
    {
        $points = 0;
        $correctPropositions = [];

        // For each questionnaire question we check if the student has chosen a good answer
        foreach ($questionnaire->getQuestions() as $question) {
            $rightPropositions = $question->getRightPropositions();

            foreach ($rightPropositions as $rightProposition) {
                $rightProposition = $rightProposition->getId();

                if ($answers->get($question->getId()) == $rightProposition) {
                    $correctPropositions[] = $rightProposition;
                    $points += $question->getScore();
                }
            }
        }

        return ['corrects' => $correctPropositions, 'points' => $points];
    }

    /**
     * Helping methodss to call breadcrumbsService.
     */
    private function questionnaireBC(?Questionnaire $questionnaire, string $method): ModelBreadcrumbs
    {
        return $this->breadCrumbs->bcQuestionnaire(
            $questionnaire,
            $method,
            $this->request->query->get('classroom_id'),
            $this->request->query->get('lesson_id'),
            $this->request->query->get('list'),
            $this->request->query->get('lonely'),
            $this->request->query->get('extra')
        );
    }

    private function breadCrumbsFunction(string $txt, string $route, ?array $params): Breadcrumbs
    {
        //         if ($request->get('classroom_id') && null === $request->get('lonely')) {
        //     $this->breadCrumbs
        //         ->bC->addRouteItem('Classe',
        //             'classroom_show',
        //             ['id' => $classroom->getId()]
        //         )
        //         ->bC->addRouteItem('Créer un Module', 'lesson_new', ['classroom_id' => $classroom->getId()])
        //         ->bC->addRouteItem('Modules', 'lesson_index', ['classroom_id' => $classroom->getId()])
        //         ->bC->addRouteItem('Module', 'lesson_show', ['id' => $lesson->getId()])
        //         ->bC->addRouteItem($txt, $route, $params)
        //     ;
        // } elseif ($request->get('lonely')) {
        //     $this->breadCrumbs
        //         ->bC->addRouteItem('Classe',
        //             'classroom_show',
        //             ['id' => $classroom->getId()]
        //         )
        //         ->bC->addRouteItem('Module', 'lesson_show', [
        //             'id' => $lesson->getId(),
        //             'classroom_id' => $classroom->getId(),
        //             'lonely' => true,
        //         ])
        //         ->bC->addRouteItem($txt, $route, $params)
        //     ;
        // } else {
        //     $this->breadCrumbs
        //         ->bC->addRouteItem('Activités', 'questionnaire_index')
        //         ->bC->addRouteItem($txt, $route, $params)
        //     ;
        // }

        return $this->breadCrumbs;
    }
}

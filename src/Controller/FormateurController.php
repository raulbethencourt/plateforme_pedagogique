<?php

namespace App\Controller;

use App\Entity\Questionnaire;
use App\Form\QuestionnaireType;
use App\Repository\QuestionnaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FormateurController
 * @package App\Controller
 * @Route("/formateur", name="formateur_")
 */
class FormateurController extends AbstractController
{
    /**
     * @var QuestionnaireRepository
     */
    private $repository;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(QuestionnaireRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     *@Route ("/", name="formateur_index")
     * @return Response
     */
    public function index()
    {
        $questionnaires = $this->repository->findAll();
        return $this->render('formateur/index.html.twig', compact('questionnaires'));
    }

    /**
     * @Route("/questionnaire/create", name="questionnaire_create")
     */
    public function newQuestionner(Questionnaire $questionnaire = null, Request $request)
    {
        if (!$questionnaire) {
            $questionnaire = new Questionnaire();
        }

        $form = $this->createForm(QuestionnaireType::class,  $questionnaire);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $questionnaire = $form->getData();

            $this->em->persist($questionnaire);
            $this->em->flush();

            $this->addFlash('success', 'questionnaire ajouté avec succès');
            return $this->redirectToRoute('formateur_formateur_index');
        }

        return $this->render('questionnaire/new.html.twig', [
            'questionnaire' => $questionnaire,
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route ("/questionnaire/{id}", name="questionnaire_edit")
     */
    public function editQuestionnaire(Questionnaire  $questionnaire, Request $request)
    {
        $form = $this->createForm(Questionnaire::class, $questionnaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'questionnaire modifié avec succes');

            return $this->redirectToRoute('formateur_formateur_index');
        }
        return $this->render('questionnaire/edit.html.twig', [
            'questionnaire' => $questionnaire,
            'form' => $form->createView()
        ]);
    }

    public function delete(Questionnaire $questionnaire, Request $request)
    {
        if () {
            $this->em->remove($questionnaire);
            $this->em->flush();
        }
    }
}

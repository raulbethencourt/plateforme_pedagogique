<?php

namespace App\Controller;

use App\Entity\Link;
use App\Form\LinkType;
use App\Form\SearchLinkType;
use App\Repository\LinkRepository;
use App\Service\BreadCrumbsService as BreadCrumbs;
use App\Service\FindEntity;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/link', name: 'link_')]
#[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_TEACHER")'))]
class LinkController extends AbstractController
{
    private $breadCrumbs;
    private $request;
    private $find;
    private $em;

    public function __construct(
        EntityManagerInterface $em,
        BreadCrumbs $breadCrumbs,
        RequestStack $request,
        FindEntity $find
    ) {
        $this->em = $em;
        $this->breadCrumbs = $breadCrumbs;
        $this->request = $request->getCurrentRequest();
        $this->find = $find;
    }

    #[Route(
        '/',
        name: 'index',
        methods: ['GET']
    )]
    public function index(LinkRepository $linkRepo, PaginatorInterface $paginator): Response
    {
        $classroom_id = $this->request->query->get('classroom_id');
        $this->breadCrumbs->bcLink(null, 'index', $classroom_id, null);

        $form = $this->createForm(SearchLinkType::class);
        $form->handleRequest($this->request);
        if ($form->isSubmitted() && $form->isValid()) {
            $name = $form->getData()['text'];
            $category = $form->getData()['category'];
            $creator = $form->getData()['author'];
            $links = $this->find->searchLink($name, $category, $creator);
            $links = $paginator->paginate($links, $this->request->query->getInt('page', 1), 10);

            return $this->render('link/index.html.twig', [
                'links' => $links,
                'classroom_id' => $classroom_id,
                'form' => $form->createView(),
            ]);
        }

        $links = $paginator->paginate($linkRepo->findAll(), $this->request->query->getInt('page', 1), 10);

        return $this->render('link/index.html.twig', [
            'links' => $links,
            'classroom_id' => $classroom_id,
            'form' => $form->createView(),
        ]);
    }

    #[Route(
        '/new',
        name: 'new',
        methods: ['GET', 'POST']
    )]
    public function new(): Response
    {
        $link = new Link();
        $link->setCreator($this->getUser()->getUserIdentifier());

        $classroom_id = $this->request->query->get('classroom_id');
        if (isset($classroom_id)) {
            $classroom = $this->find->findClassroom();
            $link->addClassroom($classroom);
        }

        $this->breadCrumbs->bcLink(null, 'new', $classroom_id, null);

        $form = $this->createForm(LinkType::class, $link);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($link);
            $this->em->flush();
            $this->addFlash('success', 'Lien créée avec succès.');

            if (isset($classroom_id)) {
                return $this->redirectToRoute('classroom_show', [
                    'id' => $classroom_id,
                ]);
            }

            return $this->redirectToRoute('link_index');
        }

        return $this->render('link/new.html.twig', [
            'link' => $link,
            'form' => $form->createView(),
            'classroom_id' => $classroom_id,
        ]);
    }

    #[Route(
        '/{id}/edit',
        name: 'edit',
        methods: ['GET', 'POST']
    )]
    public function edit(Link $link): Response
    {
        $classroom_id = $this->request->query->get('classroom_id');
        $extra = $this->request->query->get('extra');
        $this->breadCrumbs->bcLink($link, 'edit', $classroom_id, $extra);

        $form = $this->createForm(LinkType::class, $link);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush($link);
            $this->addFlash('success', 'Lien modifiée avec succès.');

            if (isset($classroom_id)) {
                return $this->redirectToRoute('classroom_show', [
                    'id' => $classroom_id,
                ]);
            }

            return $this->redirectToRoute('link_index');
        }

        return $this->render('link/edit.html.twig', [
            'link' => $link,
            'form' => $form->createView(),
            'classroom_id' => $classroom_id,
        ]);
    }

    #[Route(
        '/{id}',
        name: 'delete',
        methods: ['POST']
    )]
    public function delete(Link $link): Response
    {
        if ($this->isCsrfTokenValid('delete'.$link->getId(), $this->request->request->get('_token'))) {
            $this->em->remove($link);
            $this->em->flush();
            $this->addFlash('success', 'Lien supprimée avec succès.');
        }

        return $this->redirectToRoute('link_index');
    }
}

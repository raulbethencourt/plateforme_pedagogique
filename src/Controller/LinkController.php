<?php

namespace App\Controller;

use App\Entity\Link;
use App\Form\LinkType;
use App\Form\SearchLinkType;
use App\Repository\LinkRepository;
use App\Service\BreadCrumbsService as BreadCrumbs;
use App\Service\FindEntity;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/link")
 */
class LinkController extends AbstractController
{
    private $breadCrumbs;

    private $request;

    private $find;

    public function __construct(BreadCrumbs $breadCrumbs, RequestStack $request, FindEntity $find)
    {
        $this->breadCrumbs = $breadCrumbs;
        $this->request = $request->getCurrentRequest();
        $this->find = $find;
    }

    /**
     * @Route("/", name="link_index", methods={"GET"})
     * @Security("is_granted('ROLE_TEACHER') or is_granted('ROLE_ADMIN')")
     */
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

        $user = $this->getUser();
        if ('ROLE_ADMIN' === $user->getRoles()[0] || 'ROLE_SUPER_ADMIN' === $user->getRoles()[0]) {
            $links = $linkRepo->findAll();
        } else {
            $links = $linkRepo->findByVisibilityOrCreator($user->getUsername());
        }

        $links = $paginator->paginate($links, $this->request->query->getInt('page', 1), 10);

        return $this->render('link/index.html.twig', [
            'links' => $links,
            'classroom_id' => $classroom_id,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new", name="link_new", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_TEACHER') or is_granted('ROLE_ADMIN')")
     */
    public function new(): Response
    {
        $link = new Link();
        $link->setCreator($this->getUser()->getUsername());

        $classroom_id = $this->request->query->get('classroom_id');
        if (isset($classroom_id)) {
            $classroom = $this->find->findClassroom();
            $link->addClassroom($classroom);
        }

        $this->breadCrumbs->bcLink(null, 'new', $classroom_id, null);

        $form = $this->createForm(LinkType::class, $link);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($link);
            $entityManager->flush();
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

    /**
     * @Route("/{id}/edit", name="link_edit", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_TEACHER') or is_granted('ROLE_ADMIN')")
     */
    public function edit(Link $link): Response
    {
        $classroom_id = $this->request->query->get('classroom_id');
        $extra = $this->request->query->get('extra');
        $this->breadCrumbs->bcLink($link, 'edit', $classroom_id, $extra);

        $form = $this->createForm(LinkType::class, $link);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
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

    /**
     * @Route("/{id}", name="link_delete", methods={"DELETE"})
     */
    public function delete(Link $link): Response
    {
        if ($this->isCsrfTokenValid('delete'.$link->getId(), $this->request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($link);
            $entityManager->flush();
            $this->addFlash('success', 'Lien supprimée avec succès.');
        }

        return $this->redirectToRoute('link_index');
    }
}

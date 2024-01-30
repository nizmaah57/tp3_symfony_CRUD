<?php

namespace App\Controller\Admin;

use App\Entity\Color;
use App\Form\ColorType;
use App\Repository\ColorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/color/crud')]
class ColorCrudController extends AbstractController
{
    #[Route('/', name: 'app_color_crud_index', methods: ['GET'])]
    public function index(ColorRepository $colorRepository): Response
    {
        return $this->render('color_crud/index.html.twig', [
            'colors' => $colorRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_color_crud_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $color = new Color();
        $form = $this->createForm(ColorType::class, $color);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($color);
            $entityManager->flush();

            return $this->redirectToRoute('app_color_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('color_crud/new.html.twig', [
            'color' => $color,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_color_crud_show', methods: ['GET'])]
    public function show(Color $color): Response
    {
        return $this->render('color_crud/show.html.twig', [
            'color' => $color,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_color_crud_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Color $color, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ColorType::class, $color);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_color_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('color_crud/edit.html.twig', [
            'color' => $color,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_color_crud_delete', methods: ['POST'])]
    public function delete(Request $request, Color $color, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$color->getId(), $request->request->get('_token'))) {
            $entityManager->remove($color);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_color_crud_index', [], Response::HTTP_SEE_OTHER);
    }
}

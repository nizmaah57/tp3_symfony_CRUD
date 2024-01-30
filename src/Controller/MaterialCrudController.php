<?php

namespace App\Controller;

use App\Entity\Material;
use App\Form\MaterialType;
use App\Repository\MaterialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/material/crud')]
class MaterialCrudController extends AbstractController
{
    #[Route('/', name: 'app_material_crud_index', methods: ['GET'])]
    public function index(MaterialRepository $materialRepository): Response
    {
        return $this->render('material_crud/index.html.twig', [
            'materials' => $materialRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_material_crud_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $material = new Material();
        $form = $this->createForm(MaterialType::class, $material);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($material);
            $entityManager->flush();

            return $this->redirectToRoute('app_material_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('material_crud/new.html.twig', [
            'material' => $material,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_material_crud_show', methods: ['GET'])]
    public function show(Material $material): Response
    {
        return $this->render('material_crud/show.html.twig', [
            'material' => $material,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_material_crud_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Material $material, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MaterialType::class, $material);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_material_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('material_crud/edit.html.twig', [
            'material' => $material,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_material_crud_delete', methods: ['POST'])]
    public function delete(Request $request, Material $material, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$material->getId(), $request->request->get('_token'))) {
            $entityManager->remove($material);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_material_crud_index', [], Response::HTTP_SEE_OTHER);
    }
}

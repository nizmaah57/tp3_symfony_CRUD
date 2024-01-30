<?php

namespace App\Controller\Admin;

use App\Entity\Material;
use App\Repository\MaterialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class MaterialController extends AbstractController
{
    #[Route('/materials', name: 'app_materials', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Retourne toutes les matières.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Material::class, groups: ['material:read']))
        )
    )]
    #[OA\Tag(name: 'Matières')]
    #[Security(name: 'Bearer')]
    public function index(MaterialRepository $materialRepository): JsonResponse
    {
        $materials = $materialRepository->findAll();

        return $this->json([
            'materials' => $materials,
        ], context: [
            'groups' => ['material:read']
        ]);
    }

    #[Route('/material/{id}', name: 'app_material_get', methods: ['GET'])]
    #[OA\Tag(name: 'Matières')]
    public function get(Material $material): JsonResponse
    {
        return $this->json($material, context: [
            'groups' => ['material:read'],
        ]);
    }

    #[Route('/materials', name: 'app_material_add', methods: ['POST'])]
    #[OA\Tag(name: 'Matières')]
    public function add(Request $request, EntityManagerInterface $em): JsonResponse
    {
        try {
            // On récupère les données du corps de la requête
            // Que l'on transforme ensuite en tableau associatif
            $data = json_decode($request->getContent(), true);

            // On traite les données pour créer un nouveau Matériau
            $material = new Material();
            $material->setName($data['name']);
            

            $em->persist($material);
            $em->flush();

            return $this->json($material, context: [
                'groups' => ['material:read', 'material:create'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/material/{id}', name: 'app_material_update', methods: ['PUT', 'PATCH'])]
    #[OA\Tag(name: 'Matières')]
    public function update(Material $material, Request $request, EntityManagerInterface $em): JsonResponse
    {
        try {
            // On récupère les données du corps de la requête
            // Que l'on transforme ensuite en tableau associatif
            $data = json_decode($request->getContent(), true);

            // On traite les données pour mettre à jour le Matériau
            $material->setName($data['name']);
            // Update other properties if necessary

            $em->persist($material);
            $em->flush();

            return $this->json($material, context: [
                'groups' => ['material:read', 'material:create'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/material/{id}', name: 'app_material_delete', methods: ['DELETE'])]
    #[OA\Tag(name: 'Matières')]
    public function delete(Material $material, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($material);
        $em->flush();

        return $this->json([
            'code' => 200,
            'message' => 'Matière supprimée'
        ]);
    }
}

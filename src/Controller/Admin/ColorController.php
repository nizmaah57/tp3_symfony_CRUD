<?php

namespace App\Controller\Admin;

use App\Entity\Color;
use App\Repository\ColorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class ColorController extends AbstractController
{
    #[Route('/colors', name: 'app_colors', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Retourne toutes les couleurs.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Color::class, groups: ['color:read']))
        )
    )]
    #[OA\Tag(name: 'Couleurs')]
    #[Security(name: 'Bearer')]
    public function index(ColorRepository $colorRepository): JsonResponse
    {
        $colors = $colorRepository->findAll();

        return $this->json([
            'colors' => $colors,
        ], context: [
            'groups' => ['color:read']
        ]);
    }

    #[Route('/color/{id}', name: 'app_color_get', methods: ['GET'])]
    #[OA\Tag(name: 'Couleurs')]
    public function get(Color $color): JsonResponse
    {
        return $this->json($color, context: [
            'groups' => ['color:read'],
        ]);
    }

    #[Route('/colors', name: 'app_color_add', methods: ['POST'])]
    #[OA\Tag(name: 'Couleurs')]
    public function add(Request $request, EntityManagerInterface $em): JsonResponse
    {
        try {
            // On récupère les données du corps de la requête
            // Que l'on transforme ensuite en tableau associatif
            $data = json_decode($request->getContent(), true);

            // On traite les données pour créer une nouvelle Couleur
            $color = new Color();
            $color->setName($data['name']);
            

            $em->persist($color);
            $em->flush();

            return $this->json($color, context: [
                'groups' => ['color:read', 'color:create'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/color/{id}', name: 'app_color_update', methods: ['PUT', 'PATCH'])]
    #[OA\Tag(name: 'Couleurs')]
    public function update(Color $color, Request $request, EntityManagerInterface $em): JsonResponse
    {
        try {
            // On récupère les données du corps de la requête
            // Que l'on transforme ensuite en tableau associatif
            $data = json_decode($request->getContent(), true);

            // On traite les données pour mettre à jour la Couleur
            $color->setName($data['name']);
            // Update other properties if necessary

            $em->persist($color);
            $em->flush();

            return $this->json($color, context: [
                'groups' => ['color:read', 'color:create'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/color/{id}', name: 'app_color_delete', methods: ['DELETE'])]
    #[OA\Tag(name: 'Couleurs')]
    public function delete(Color $color, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($color);
        $em->flush();

        return $this->json([
            'code' => 200,
            'message' => 'Couleur supprimée'
        ]);
    }
}

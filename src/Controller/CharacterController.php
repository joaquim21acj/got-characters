<?php

namespace App\Controller;

use App\Entity\Character;
use App\Form\CharacterType;
use App\Repository\CharacterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Annotations as OA;
//use AppBundle\Entity\Reward;
//use AppBundle\Entity\User;
use Nelmio\ApiDocBundle\Annotation\Model;
//use Nelmio\ApiDocBundle\Annotation\Security;

#[Route('/character')]
class CharacterController extends AbstractController
{
    /**
     * @OA\Get(
     *     path="/character",
     *     summary="Get a list of characters",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=Character::class)))
     *     )
     * )
     */
    #[Route('/', name: 'app_character_index', methods: ['GET'])]
    public function index(CharacterRepository $characterRepository): Response
    {
        return $this->render('character/index.html.twig', [
            'characters' => $characterRepository->findAll(),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/character/new",
     *     summary="Create a new character",
     *     @OA\RequestBody(
     *         @OA\JsonContent(ref=@Model(type=CharacterType::class))
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Character created",
     *         @OA\JsonContent(ref=@Model(type=Character::class))
     *     )
     * )
     */
    #[Route('/new', name: 'app_character_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $character = new Character();
        $form = $this->createForm(CharacterType::class, $character);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($character);
            $entityManager->flush();

            return $this->redirectToRoute('app_character_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('character/new.html.twig', [
            'character' => $character,
            'form' => $form,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/character/{id}",
     *     summary="Get a specific character",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref=@Model(type=Character::class))
     *     )
     * )
     */
    #[Route('/{id}', name: 'app_character_show', methods: ['GET'])]
    public function show(Character $character): Response
    {
        return $this->render('character/show.html.twig', [
            'character' => $character,
        ]);
    }

    /**
     * @OA\Put(
     *     path="/character/{id}/edit",
     *     summary="Edit a character",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(ref=@Model(type=CharacterType::class))
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Character updated",
     *         @OA\JsonContent(ref=@Model(type=Character::class))
     *     )
     * )
     */
    #[Route('/{id}/edit', name: 'app_character_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Character $character, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CharacterType::class, $character);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_character_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('character/edit.html.twig', [
            'character' => $character,
            'form' => $form,
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/character/{id}",
     *     summary="Delete a character",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Character deleted"
     *     )
     * )
     */
    #[Route('/{id}', name: 'app_character_delete', methods: ['POST'])]
    public function delete(Request $request, Character $character, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$character->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($character);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_character_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/search/{name}", name="entity1_search")
     */
    public function search(CharacterRepository $repository, string $name): Response
    {
        $entities = $repository->findByNameOrHouse($name);

        return $this->render('character/index.html.twig', [
            'characters' => $entities->findAll(),
        ]);
    }
}

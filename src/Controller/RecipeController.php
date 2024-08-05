<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use function Symfony\Component\Clock\now;

class RecipeController extends AbstractController
{
    #[Route('/recettes/{slug}-{id}', name: 'recipes.show', requirements: ['id' => '\d+', 'slug' => '[a-z0-9-]+'])]
    public function show(Request $request, RecipeRepository $recipeRepository, string $slug, int $id): Response
    {

        $recette = $recipeRepository->findOneById($id);
        if ($recette->getSlug() !== $slug ) {
            return $this->redirectToRoute('recipe.show', ['slug' => $recette->getSlug(), 'id' => $recette->getId()]);
        }

        return $this->render('recipe/show.html.twig', [
            'nom' => $slug.' '.$id,
            'recette' => $recette
        ]);
    }

    #[Route('/recettes/{id}/edit', name: 'recipes.edit', requirements: ['id' => '\d+'], methods:['GET', 'POST'])]
    public function edit(Recipe $recette, Request $request, EntityManagerInterface $em): Response
    {
        
        $form = $this->createForm(RecipeType::class, $recette);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'La recette a bien été modifiée');
            return $this->redirectToRoute('recipes');
        }

        return $this->render('recipe/edit.html.twig', [
            'recette' => $recette,
            'form' => $form,
        ]);
    }

    #[Route('/recettes', name: 'recipes')]
    public function index(Request $request, RecipeRepository $recipeRepository): Response
    {  
        $recettes = $recipeRepository->findAll();

        return $this->render('recipe/index.html.twig', [
            'titre' => 'Nos recettes',
            'recettes' => $recettes,
        ]);
    }

    #[Route('/recettes/new', name: 'recipes.add')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {  
        $recette = new Recipe();
        $form = $this->createForm(RecipeType::class, $recette);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($recette);
            $em->flush();
            $this->addFlash('success', 'La recette a bien été créée');
            return $this->redirectToRoute('recipes');
        }

        return $this->render('recipe/add.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/recettes/{id}/delete', name: 'recipes.delete', methods:['DELETE'])]
    public function delete(Recipe $recette, EntityManagerInterface $em): Response
    {  
          
        $em->remove($recette);
        $em->flush();
        $this->addFlash('success', 'La recette a bien été supprimée');
        return $this->redirectToRoute('recipes');
    
    }
}

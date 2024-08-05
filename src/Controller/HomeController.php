<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(Request $request, RecipeRepository $recipeRepository): Response
    {

        $recettes = $recipeRepository->findWithDurationLowerThan(30);
        
        return $this->render('home/index.html.twig', [
            'recettes' => $recettes
        ]);
    }
}

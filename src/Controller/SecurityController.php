<?php

namespace App\Controller;

use App\Repository\ArtisanRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/artisan')]
class SecurityController extends AbstractController
{
    #[Route('/', name: 'app_artisan')]
    public function index(ArtisanRepository $artisanRepository): Response
    {
        return $this->render('artisan/index.html.twig', [
            'artisans' => $artisanRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_artisan')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $artisan = new Artisan();
        $form = $this->createForm(ArtisanType::class, $artisan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($artisan);
            $entityManager->flush();

            return $this->redirectToRoute('artisan_index');
        }

        return $this->render('artisan/new.html.twig', [
            'artisan' => $artisan,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/update', name: 'app_artisan')]
    public function update(): Response
    {
        return $this->render('artisan/index.html.twig', [
            'controller_name' => 'ArtisanController',
        ]);
    }

    #[Route('/show', name: 'app_artisan')]
    public function show(): Response
    {
        return $this->render('artisan/index.html.twig', [
            'controller_name' => 'ArtisanController',
        ]);
    }

    #[Route('/delete', name: 'app_artisan')]
    public function delete(): Response
    {
        return $this->render('artisan/index.html.twig', [
            'controller_name' => 'ArtisanController',
        ]);
    }
}

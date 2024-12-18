<?php

namespace App\Controller;

use App\Entity\Alteret;
use App\Entity\Commande;
use App\Form\AlteretType;
use App\Repository\AlteretRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/Alteret')]
class AlteretController extends AbstractController
{
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    #[Route('/', name: 'app_Alteret_index', methods: ['GET'])]
    public function index(AlteretRepository $AlteretRepository): Response
    {
        return $this->render('Alteret/index.html.twig', [
            'Alterets' => $AlteretRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_Alteret_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $Alteret = new Alteret();
        $form = $this->createForm(AlteretType::class, $Alteret);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('photo')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $this->slugger->slug($originalFilename); 
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'), 
                        $newFilename
                    );
                } catch (FileException $e) {
                }

                $Alteret->setPhoto($newFilename);
            }

            $entityManager->persist($Alteret);
            $entityManager->flush();

            return $this->redirectToRoute('app_Alteret_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('Alteret/new.html.twig', [
            'Alteret' => $Alteret,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_Alteret_show', methods: ['GET'])]
    public function show(Alteret $Alteret): Response
    {
        return $this->render('Alteret/show.html.twig', [
            'Alteret' => $Alteret,
        ]);
    }
    #[Route('/{id}/edit', name: 'app_Alteret_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, Alteret $Alteret, EntityManagerInterface $entityManager): Response
{
    $form = $this->createForm(AlteretType::class, $Alteret);
    $form->handleRequest($request);

    $originalPhoto = $Alteret->getPhoto();

    if ($form->isSubmitted() && $form->isValid()) {
        $imageFile = $form->get('photo')->getData();

        if ($imageFile) {
            if ($originalPhoto) {
                $oldImagePath = $this->getParameter('images_directory') . '/' . $originalPhoto;
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $this->slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

            try {
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
            }

            $Alteret->setPhoto($newFilename);
        } else {
            $Alteret->setPhoto($originalPhoto);
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_Alteret_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->renderForm('Alteret/edit.html.twig', [
        'Alteret' => $Alteret,
        'form' => $form,
    ]);
}

    
    
    #[Route('/{id}', name: 'app_Alteret_delete', methods: ['POST'])]
public function delete(Request $request, Alteret $Alteret, EntityManagerInterface $entityManager): Response
{
    if ($this->isCsrfTokenValid('delete'.$Alteret->getId(), $request->request->get('_token'))) {
        // Find and remove all commandes related to this Alteret
        $commandes = $entityManager->getRepository(Commande::class)->findBy(['Alteret' => $Alteret]);
        foreach ($commandes as $commande) {
            $entityManager->remove($commande);
        }

        // Now remove the Alteret
        $entityManager->remove($Alteret);
        $entityManager->flush();
    }

    return $this->redirectToRoute('app_Alteret_index', [], Response::HTTP_SEE_OTHER);
}

}

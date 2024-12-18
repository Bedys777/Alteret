<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AlteretRepository;
use App\Entity\Alteret;
use App\Entity\Commande;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_default')]
    public function index(AlteretRepository $lr): Response
    {
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'Alterets' => $lr->findAll(),
        ]);
    }
    #[Route('/item/{id}', name: 'app_item')]
    public function indexitem(AlteretRepository $lr, int $id): Response
    {
        $Alteret = $lr->find($id);
        if (!$Alteret) {
            throw $this->createNotFoundException('Produit non trouvé.');
        }
        return $this->render('default/item.html.twig', [
            'Alteret' => $Alteret, 
        ]);
    }




#[Route('/achat/{id}', name: 'app_default_achat', methods: ['POST'])]
// #[IsGranted('ROLE_USER')]
    public function acheter(
        Alteret $Alteret,
        EntityManagerInterface $entityManager,
        Security $security
    ): Response {
        if ($Alteret->getQuantite() <= 0) {
            $this->addFlash('danger', 'Cette Alteret est indisponible.');
            return $this->redirectToRoute('app_default');
        }

        $user = $security->getUser();
        if (!$user) {
            $this->addFlash('danger', 'Vous devez être connecté pour effectuer cet achat.');
            return $this->redirectToRoute('app_login');
        }

        $commande = new Commande();
        $commande->setAlteret($Alteret);
        $commande->setQuantite(1); 
        $commande->setPrix($Alteret->getPrix());
        $commande->setDate(new \DateTime());

        $commande->setUser($user);

        $Alteret->setQuantite($Alteret->getQuantite() - 1);

        $entityManager->persist($commande);
        $entityManager->persist($Alteret);
        $entityManager->flush();

    $this->addFlash('success', 'Commande effectuée avec succès.');
    return $this->redirectToRoute('app_default', ['success' => 1]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Products;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class DefaultController extends AbstractController
{
    private $entityManager;
    private $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    #[Route('/', name: 'app_default')]
    public function index(): Response
    {
        $productsRepository = $this->entityManager->getRepository(Products::class);
        $products = $productsRepository->findBy(['available' => true]);

        $isLoggedIn = $this->security->isGranted('ROLE_USER');

        return $this->render('homepage.html.twig', [
            'controller_name' => 'DefaultController',
            'products' => $products,
            'isLoggedIn' => $isLoggedIn,
        ]);
    }

    #[Route('/statistieken', name: 'app_statistieken')]
    public function viewStatistics(): Response
    {
        return $this->render('statistieken.html.twig');
    }

    #[Route('/product/{name}', name: 'product_view')]
    public function viewProduct($name): Response
    {
        $product = $this->entityManager->getRepository(Products::class)->findOneBy(['name' => $name]);

        if (!$product) {
            throw $this->createNotFoundException('The product does not exist');
        }

        return $this->render('product.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/contact', name: 'app_contact')]
    public function viewcontact(): Response
    {
        return $this->render('contact.html.twig');
    }


}

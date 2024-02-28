<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;

class OrderHistoryController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/bestellingen', name: 'app_order_history')]
    public function index(): Response
    {
        $user = $this->getUser();
        $orders = $this->entityManager->getRepository(Order::class)->findBy(['username' => $user]);

        return $this->render('order_history.html.twig', [
            'orders' => $orders,
        ]);
    }
}

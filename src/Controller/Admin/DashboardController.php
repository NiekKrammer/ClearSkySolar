<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Products;
use App\Entity\User;
use App\Entity\Order;
use App\Entity\OrderItems;

class DashboardController extends AbstractDashboardController
{

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
       return $this->render('admin.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('ClearSkySolar');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('Producten', 'fa fa-utensils', Products::class);
        yield MenuItem::linkToCrud('Gebruikers', 'fa fa-users', User::class);
        yield MenuItem::linkToCrud('Bestellingen', 'fa-solid fa-table-list', Order::class);
        yield MenuItem::linkToCrud('Bestelde items', 'fa-solid fa-list-ul', OrderItems::class);
        yield MenuItem::linkToUrl('Ga terug', 'fa-solid fa-arrow-left', $this->generateUrl('app_default'));
    }
}

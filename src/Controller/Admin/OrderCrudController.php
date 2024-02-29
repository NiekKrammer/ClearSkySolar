<?php
namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use App\Entity\Order;

class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            Field::new('id', 'id'),
            Field::new('name', 'naam'),
            Field::new('address', 'adres'),
            Field::new('email', 'email'),
            Field::new('phoneNr', 'telefoonnummer'),
            Field::new('date', 'afspraak datum'),
            Field::new('time', 'afspraak tijd'),
            Field::new('ordered_at', 'besteld op'),
        ];
    }
}

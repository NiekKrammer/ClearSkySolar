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
            Field::new('id', 'ID'),
            Field::new('name', 'Name'),
            Field::new('address', 'Address'),
            Field::new('email', 'Email'),
            Field::new('phoneNr', 'Phone Number'),
            Field::new('date', 'Chosen Date'),
            Field::new('time', 'Chosen Time'),
            Field::new('ordered_at', 'Ordered at'),
        ];
    }
}

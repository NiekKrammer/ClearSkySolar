<?php
namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use App\Entity\Order;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

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
            Field::new('name', 'Naam'),
            Field::new('address', 'Adres'),
            Field::new('email', 'E-mail'),
            Field::new('phoneNr', 'Telefoonnummer'),
            Field::new('date', 'Afspraak Datum'),
            Field::new('time', 'Afspraak Tijd'),
            Field::new('ordered_at', 'Besteld op'),
            AssociationField::new('username', 'Gebruikersnaam')->formatValue(function ($value, $entity) {
                return $value ? $value->getUsername() : '';
            }),
        ];
    }

}

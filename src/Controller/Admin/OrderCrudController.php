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
            Field::new('id', 'id'),
            Field::new('voornaam', 'voornaam'),
            Field::new('achternaam', 'achternaam'),
            Field::new('postcode', 'postcode'),
            Field::new('straatnaam', 'straatnaam'),
            Field::new('huisnummer', 'huisnummer'),
            Field::new('email', 'e-mail'),
            Field::new('phoneNr', 'telefoonnummer'),
            Field::new('date', 'afspraak Datum'),
            Field::new('time', 'afspraak Tijd'),
            Field::new('ordered_at', 'besteld op'),
            AssociationField::new('username', 'gebruikersnaam')->formatValue(function ($value, $entity) {
                return $value ? $value->getUsername() : '';
            }),
        ];
    }

}

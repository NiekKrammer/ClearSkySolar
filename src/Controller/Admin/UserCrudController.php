<?php
namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use App\Entity\User;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            Field::new('id', 'id'),
            Field::new('username', 'gebruikersnaam'),
            Field::new('email', 'email'),
            Field::new('created_at', 'aangemaakt op'),
        ];
    }
}

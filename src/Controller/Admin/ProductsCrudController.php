<?php

namespace App\Controller\Admin;

use App\Entity\Products;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProductsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Products::class;
    }
    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->setFormTypeOption('disabled', true);
        yield Field::new('name');
        yield Field::new('price');
        yield BooleanField::new('available')->setFormTypeOption('data', true);
        yield ImageField::new('image_file', 'Image')
            ->setRequired(false)
            ->setBasePath('product_imgs')
            ->setUploadDir('public/product_imgs')
            ->setUploadedFileNamePattern('[randomhash].[extension]');
        yield TextField::new('description');
        yield Field::new('info');
    }
}

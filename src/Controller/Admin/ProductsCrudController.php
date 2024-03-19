<?php

namespace App\Controller\Admin;

use App\Entity\Products;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProductsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Products::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->setLabel('id')->setFormTypeOption('disabled', true);
        yield BooleanField::new('available')->setLabel('beschikbaar')->setFormTypeOption('data', true);
        yield Field::new('name')->setLabel('naam');
        yield ImageField::new('image_file')->setLabel('foto')
            ->setRequired(false)
            ->setBasePath('product_imgs')
            ->setUploadDir('public/product_imgs')
            ->setUploadedFileNamePattern('[randomhash].[extension]');
        yield TextareaField::new('info2', 'Informatie vervolgpagina')->renderAsHtml();
        yield TextField::new('description')->setLabel('beschrijving')
            ->setRequired(false);
        yield Field::new('price')->setLabel('prijs');
        yield TextField::new('quantity')->setLabel('hoeveelheid');
        yield TextEditorField::new('info')->setLabel('informatie vervolgpagina');
    }

}

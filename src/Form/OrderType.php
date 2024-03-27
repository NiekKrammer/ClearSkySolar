<?php

namespace App\Form;

use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('voornaam', TextType::class, [
                'label' => 'Voornaam',
                "required" => true,
            ])
            ->add('achternaam', TextType::class, [
                'label' => 'Achternaam',
                "required" => true,
            ])
            ->add('postcode', TextType::class, [
                'label' => 'Postcode',
                "required" => true,
                'attr' => [
                    'class' => 'w-25 postcode',
                ]
            ])
            ->add('huisnummer', TextType::class, [
                'label' => 'Huisnummer',
                "required" => true,
                'attr' => [
                    'class' => 'w-25 huisnummer',
                ]
            ])
            ->add('city', TextType::class, [
                'label' => 'Stad',
                "required" => true,
                'attr' => [
                    'class' => 'city',
                ]
            ])
            ->add('straatnaam', TextType::class, [
                'label' => 'Straatnaam',
                "required" => true,
                'attr' => [
                    'class' => 'straatnaam',
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                "required" => true,
            ])
            ->add('phoneNr', TelType::class, [
                'label' => 'Telefoonnummer',
                'required' => true,
            ])
            ->add('date', DateType::class, [
                'label' => 'Kies een beschikbare afspraakdatum',
                "required" => true,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'datepicker w-50',
                    'readonly' => 'readonly'
                ]
            ])
            ->add('time', TimeType::class, [
                'label' => 'Tijdstip',
                "required" => true,
                'data' => new \DateTime('now'),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\Order',
        ]);
    }
}

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
            ->add('name', TextType::class, [
                'label' => 'Naam',
                "required" => true,
            ])
            ->add('address', TextType::class, [
                'label' => 'Adres',
                "required" => true,
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                "required" => true,
            ])
            ->add('phoneNr', TelType::class, [
                'label' => 'Telefoonnummer',
                'required' => true,
                'attr' => [
                    'class' => 'w-50',
                ]
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

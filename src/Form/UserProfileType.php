<?php

namespace App\Form;

use App\Entity\User; // Assurez-vous d'importer votre entité User
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType; // Importer le type EmailType
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [ // Ajout du champ email
                'label' => 'Email',
                'required' => true,
                'attr' => ['readonly' => true], // Pour le rendre en lecture seule
            ])
            ->add('name', TextType::class, [
                'label' => 'Username',
                'required' => false,
            ])
            ->add('firstname', TextType::class, [
                'label' => 'First Name',
                'required' => false,
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Last Name',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

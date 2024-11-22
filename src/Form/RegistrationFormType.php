<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\User;
use Symfony\Component\Validator\Constraints\NotBlank;


class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => ['placeholder' => 'email_placeholder'],
                'required' => true,
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('name', TextType::class, [
                'attr' => ['placeholder' => 'username_placeholder'],
                'required' => true,
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('password', PasswordType::class, [
                'attr' => ['placeholder' => 'password_placeholder'],
                'required' => true,
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('confirm_password', PasswordType::class, [
                'attr' => ['placeholder' => 'confirm_password_placeholder'],
                'required' => true,
                'mapped' => false,
                'constraints' => [
                    new NotBlank()
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

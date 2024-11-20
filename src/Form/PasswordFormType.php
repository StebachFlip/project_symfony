<?php 
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Model\PasswordChange;

class PasswordFormType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('oldPassword', PasswordType::class, [
                'attr' => ['placeholder' => 'old_pass'],
                'required' => false,
            ])
            ->add('newPassword', PasswordType::class, [
                'attr' => ['placeholder' => 'new_pass'],
                'required' => false,
            ])
            ->add('confirmPassword', PasswordType::class, [
                'attr' => ['placeholder' => 'confirm_pass'],
                'required' => false,
            ]);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PasswordChange::class,
        ]);
    }
}
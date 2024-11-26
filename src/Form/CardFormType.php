<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Card;
use Symfony\Component\Validator\Constraints\NotBlank;


class CardFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('number', TextType::class, [
                'attr' => ['placeholder' => 'number_placeholder'],
                'required' => true,
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('expirationDate', TextType::class, [
                'attr' => ['placeholder' => 'date_placeholder'],
                'required' => true,
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('cvv', TextType::class, [
                'attr' => ['placeholder' => 'cvv_placeholder'],
                'required' => true,
                'constraints' => [
                    new NotBlank()
                ]
                ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Card::class,
        ]);
    }
}

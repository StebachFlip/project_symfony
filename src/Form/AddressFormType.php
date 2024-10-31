<?php 
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('street', TextType::class, [
                'attr' => ['placeholder' => 'address_placeholder'],
                'required' => false,
            ])
            ->add('postalCode', TextType::class, [
                'attr' => ['placeholder' => 'postalCode_placeholder'],
                'required' => false,
            ])
            ->add('city', TextType::class, [
                'attr' => ['placeholder' => 'city_placeholder'],
                'required' => false,
            ])
            ->add('country', TextType::class, [
                'attr' => ['placeholder' => 'country_placeholder'],
                'required' => false,
            ]);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}

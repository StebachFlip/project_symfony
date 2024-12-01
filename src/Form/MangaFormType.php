<?php
namespace App\Form;

use App\Entity\Manga;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;  // Import de TextAreaType
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MangaFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Définition des champs du formulaire
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du Manga',
                'attr' => ['placeholder' => 'Entrez le nom du manga'],
                'required' => true,
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('author', TextType::class, [
                'label' => 'Auteur du Manga',
                'attr' => ['placeholder' => 'Entrez l\'auteur du manga'],
                'required' => true,
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('description', TextAreaType::class, [
                'label' => 'Description du Manga',
                'attr' => [
                    'placeholder' => 'Entrez la description du manga',
                    'rows' => 6,
                    'cols' => 40 
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank()
                ]
            ]);
           
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Manga::class, // Liaison avec l'entité Manga
        ]);
    }
}

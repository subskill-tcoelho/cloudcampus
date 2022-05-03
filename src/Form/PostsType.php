<?php
namespace App\Form;

use App\Entity\Categories;
use App\Entity\Posts;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PostsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event){
                $post = $event->getdata();
                $form = $event->getForm();

                //Check if the product object is "new" -> empty
                //If no data is passed to the form, the data is null
                //This should be considered a new object
                if(!$post || null === $post->getId()){
                    $form->add('save', SubmitType::class, ['label' => 'Nouvel Article']);
                }
            })
            ->add('title', TextType::class)
            ->add('content', TextareaType::class)
            ->add('categories', EntityType::class, ['class'=> Categories::class, 'choice_label' => 'title', 'multiple' => true])

            ->add('image', FileType::class, [
                'label' => 'image',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File(
                    [
                        'maxSize' => '10024K',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'please upload a valid image'
                    ]
                )]
                ])
            ->add('author', EntityType::class, ['class'=> User::class, 'choice_label' => 'fullname'])
            ->add('save', SubmitType::class, ['label'=>'Modifier l\'article']);
    }

    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults([
            'data_class' => Posts::class,
        ]);
    }
}
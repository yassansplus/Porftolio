<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
class PresentationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, ["attr"=>["class"=>"form-control"]])
            ->add('twitter', TextType::class, ["attr"=>["class"=>"form-control"]])
            ->add('phone', TextType::class, ["attr"=>["class"=>"form-control"]])
            ->add('email', TextType::class, ["attr"=>["class"=>"form-control"]])
            ->add('titre', TextType::class, ["attr"=>["class"=>"form-control"]])
            ->add('github', TextType::class, ["attr"=>["class"=>"form-control"]])
            ->add('shortDescription',CKEditorType::class , ["attr"=>["class"=>"form-control"],'config' => ['toolbar' => 'my_toolbar_1']])
            ->add('description',CKEditorType::class , ["attr"=>["class"=>"form-control"],'config' => ['toolbar' => 'my_toolbar_1']]);
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Presentation'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_presentation';
    }


}

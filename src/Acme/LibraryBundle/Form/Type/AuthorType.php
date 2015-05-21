<?php
// src/Acme/LibraryBundle/Form/Type/AuthorType.php

namespace Acme\LibraryBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Acme\LibraryBundle\Model\AuthorPeer;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AuthorType extends AbstractType
{
   
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
         $builder->add('first_name', 'text', array(
                'constraints' => array(
           new NotBlank())));
        $builder->add('last_name', 'text', array(
                'constraints' => array(
           new NotBlank())));
        
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acme\LibraryBundle\Model\Author',
        ));
    }

    public function getName()
    {
        return 'author';
    }
}
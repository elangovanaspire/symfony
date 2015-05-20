<?php
// src/Acme/LibraryBundle/Form/Type/AuthorType.php

namespace Acme\LibraryBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AuthorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('first_name');
        $builder->add('last_name');
        $builder->add('books', 'collection', array(
            'type'          => new \Acme\LibraryBundle\Form\Type\BookType(),
            'allow_add'     => true,
            'allow_delete'  => true,
            'by_reference'  => false,
        ));
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
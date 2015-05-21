<?php

namespace Acme\LibraryBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       
        $builder->add('title','text', array(
                'constraints' => array(
           new NotBlank())));
        $builder->add('isbn','text', array(
                'constraints' => array(
           new NotBlank())));
        // Author relation
        // $builder->add('author', new AuthorType());
         $builder->add('author', new AuthorType()); 
         
         // $builder->add('book_club_list', new BookClubListType()); 
         
       //$builder->add('author', new AuthorType());
       /* $builder->add('author', 'model', array(
            'placeholder' => 'Choose an Author',
            'class' => 'Acme\LibraryBundle\Model\Author',
            'property' => 'last_name',
            'index_property' => 'id' 
        ));*/
       
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acme\LibraryBundle\Model\Book',
        ));
    }

    public function getName()
    {
        return 'book';
    }
}
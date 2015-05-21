<?php


namespace Acme\LibraryBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BookClubListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('group_leader');
        $builder->add('theme');
        // Book collection
        $builder->add('books', 'collection', array(
            'type'          => new \Acme\LibraryBundle\Form\Type\BookType(),
            'allow_add'     => true,
            'allow_delete'  => true,
            'by_reference'  => false
        ));
        $builder->add('author', new AuthorType());
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acme\LibraryBundle\Model\BookClubList',
        ));
    }

    public function getName()
    {
        return 'book_club_list';
    }
}
<?php
/**
 * @author Dmitry Groza <boxfrommars@gmail.com>
 */

namespace Book;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class BookForm extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('title', 'text', array(
                'label' => 'Заголовок',
                'attr' => array(
                    'class' => 'input-block-level'
                ),
//                'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 5)))
                'constraints' => array(new Assert\NotBlank())
            ))
//            ->add('file_epub', 'file', array(
//                'label' => 'fb2',
//            ))
//            ->add('file_fb2', 'file', array(
//                'label' => 'fb2',
//            ))
//            ->add('image', 'file', array(
//                'label' => 'обложка',
//            ))
            ->add('author', 'text', array(
                'label' => 'Автор',
                'attr' => array(
                    'class' => 'input-block-level'
                ),
            ))
            ->add('image', 'text', array(
                'label' => 'Обложка',
                'attr' => array(
                    'class' => 'file file-image',
//                    'disabled' => 'disabled',
                ),
            ))
            ->add('file_fb2', 'text', array(
                'label' => 'fb2',
                'attr' => array(
                    'class' => 'file',
//                    'disabled' => 'disabled',
                ),
            ))
            ->add('file_epub', 'text', array(
                'label' => 'epub',
                'attr' => array(
                    'class' => 'file',
//                    'disabled' => 'disabled',
                ),
            ))
            ->add('content', 'textarea', array(
                'label' => 'Контент',
                'attr' => array(
                    'class' => 'input-block-level'
                ),
            ))
            ->add('is_published', 'checkbox', array(
                'label' => 'Опубликована',
                'required' => false,
            ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'book';
    }
}
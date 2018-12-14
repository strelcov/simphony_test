<?php

namespace AppBundle\Form;

use AppBundle\Entity\Author;
use AppBundle\Entity\Book;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['required' => 'required', 'label' => 'Название'])
            ->add('screen', FileType::class, [
                'label' => 'Обложка, Формат: png, jpg, до 5Mb',
                'data_class' => null,
                'required' => false,
            ])
            ->add('filePath', FileType::class, [
                'label' => 'Файл книги, Размер до 5Mb',
                'data_class' => null,
                'required' => false,
            ])
            ->add('readDate', DateTimeType::class, ['label' => 'Дата прочтения'])
            ->add('allowDownload', CheckboxType::class, [
                'label' => 'Разрешить скачивание',
                'required' => false,
            ])
            ->add('author');
            /*->add('author', CollectionType::class, [
                'entry_type' => Author::class,
                'allow_add' => true,
                'choice_label' => 'name',
                'by_reference' => true,
            ]);*/
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Book::class
        ));
    }


    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'appbundle_book';
    }


}

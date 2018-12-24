<?php

namespace AppBundle\Form;

use AppBundle\Entity\Author;
use AppBundle\Entity\Book;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;

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
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Image([
                        'maxSize' => '5M',
                        'maxSizeMessage' => 'Максимальный размер файла 5MB.',
                        'mimeTypes' => ["image/png", "image/jpg", "image/jpeg"],
                        'mimeTypesMessage' => 'Необходимо загрузить изображение',
                    ])
                ],
            ])
            ->add('filePath', FileType::class, [
                'label' => 'Файл книги, Размер до 5Mb',
                'mapped' => false,
                'data_class' => null,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'maxSizeMessage' => 'Максимальный размер файла 5MB.',
                    ])
                ],
            ])
            ->add('readDate', DateType::class, [
                'label' => 'Дата прочтения',
                'widget' => 'single_text',
                'html5' => true,
                'required' => 'required',
            ])
            ->add('allowDownload', CheckboxType::class, [
                'label' => 'Разрешить скачивание',
                'required' => false,
            ])
            //->add('author');
            ->add('author', EntityType::class, [
                'class' => Author::class,
                'label' => 'Автор',
                'choice_label' => 'name',
                'required' => 'required',
            ]);
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

<?php

namespace EDB\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function array_merge;

class TinyMceType extends AbstractType
{
    public function getParent()
    {
        return TextareaType::class;
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
            'editor_css' => false,
            'height' => 250,
            'show_menu' => false,
            'tinymce_formats' => false,
            'tinymce_plugins' => 'autolink lists link table wordcount',
            'tinymce_style_formats' => false,
            'tinymce_toolbar' => 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect styleselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | link media',
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars = array_merge($view->vars, $options);
    }
}
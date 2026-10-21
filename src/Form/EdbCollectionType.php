<?php

declare(strict_types=1);

namespace EDB\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EdbCollectionType extends AbstractType
{
    public function getParent(): string
    {
        return CollectionType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setNormalizer('entry_options', function(Options $options, $value) {
            return array_merge([
                'label' => false
            ], $value);
        });

        $resolver->setDefaults([
            'by_reference' => false,
            'allow_add' => true,
            'required' => false,
            'allow_delete' => true,
            'sortable' => false,
        ]);
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['sortable'] = $options['sortable'];
    }
}

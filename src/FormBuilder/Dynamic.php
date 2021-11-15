<?php

namespace EDB\AdminBundle\FormBuilder;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Dynamic extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['form_collection']) return;
        foreach ($options['form_collection']->getElements() as $formItem) {
            /** @var FormItem $formItem */
            $builder->add($formItem->getName(), $formItem->getType(), $formItem->getOptions());
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
            'form_collection' => null
        ]);
    }
}

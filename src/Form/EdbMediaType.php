<?php

declare(strict_types=1);

namespace EDB\AdminBundle\Form;

use Exception;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EdbMediaType extends AbstractType
{
    private string $mediaClass;

    public function __construct(string $mediaClass)
    {
        if (!$mediaClass) {
            throw new Exception('No media class is implemented in this project.');
        }

        $this->mediaClass = $mediaClass;
    }

    public function getParent(): string
    {
        return EntityType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'class' => $this->mediaClass,
            'preview_route_name' => 'media_preview',
            'mime_types' => []
        ]);
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        parent::finishView($view, $form, $options);

        $view->vars['preview_route_name'] = $options['preview_route_name'];
        $view->vars['mime_types'] = $options['mime_types'];
    }
}

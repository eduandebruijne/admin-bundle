<?php

declare(strict_types=1);

namespace EDB\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class EdbCollectionType extends AbstractType
{
    public function getParent()
    {
        return CollectionType::class;
    }
}

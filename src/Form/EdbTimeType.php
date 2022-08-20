<?php

declare(strict_types=1);

namespace EDB\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;

class EdbTimeType extends AbstractType
{
    public function getParent()
    {
        return TimeType::class;
    }
}

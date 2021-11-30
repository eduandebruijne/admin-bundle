<?php

declare(strict_types=1);

namespace EDB\AdminBundle\FormBuilder;

use EDB\AdminBundle\Collection\AbstractCollection;
use Symfony\Component\Form\CallbackTransformer;

class FormCollection extends AbstractCollection
{
    private $modelTransformer;

    public function add($name, $type, $options = []): FormCollection
    {
        $this->elements[] = new FormItem($name, $type, $options);
        return $this;
    }

	public function getModelTransformer(): ?CallbackTransformer
	{
		return $this->modelTransformer;
	}

	public function setModelTransformer(CallbackTransformer $modelTransformer)
	{
		$this->modelTransformer = $modelTransformer;
	}
}

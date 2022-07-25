<?php

namespace EDB\AdminBundle\Collection;

abstract class AbstractCollectionElement
{
    protected array $options;

    protected string $name;

    public function __construct(string $name, array $options)
    {
        $this->name = $name;
        $this->options = $options;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getOption(string $name)
    {
        if (isset($this->options[$name])) {
            return $this->options[$name];
        }

        return null;
    }
}

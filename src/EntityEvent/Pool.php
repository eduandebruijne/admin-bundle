<?php

namespace EDB\AdminBundle\EntityEvent;

use EDB\AdminBundle\Entity\BaseEntity;

class Pool
{
    const CREATE_CONTEXT = 'create';
    const READ_CONTEXT = 'read';
    const UPDATE_CONTEXT = 'update';
    const DELETE_CONTEXT = 'delete';

    private array $handlers = [];

    public function __construct($handlers)
    {
        foreach ($handlers as $handler) {
            /** @var EntityEventHandlerInterface $handler */
            if (!isset($this->handlers[$handler->getPriority()])) {
                $this->handlers[$handler->getPriority()] = [];
            }
            $this->handlers[$handler->getPriority()][] = $handler;
        }
    }

    public function handleEvents(BaseEntity $entity, string $context)
    {
        foreach ($this->handlers as $p => $handlers) {
            foreach ($handlers as $handler) {
                /** @var EntityEventHandlerInterface $handler */
                $supported = $handler->supports();
                if (!$this->doesSupportClass($supported, $entity) && !in_array("*", $supported)) {
                    continue;
                }

                $handler->handle($entity, $context);
            }
        }
    }

    private function doesSupportClass(array $supported, BaseEntity $instance): bool
    {
        foreach($supported as $supportedClass) {
            if ($instance instanceof $supportedClass) {
                return true;
            }
        }
        return false;
    }
}

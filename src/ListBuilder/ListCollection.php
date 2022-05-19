<?php

declare(strict_types=1);

namespace EDB\AdminBundle\ListBuilder;

use EDB\AdminBundle\Collection\AbstractCollection;

class ListCollection extends AbstractCollection
{
    public function add(string $name, array $options = []): ListCollection
    {
        $this->elements[] = new Column($name, array_merge([
            Column::OPTION_TEMPLATE => '@EDBAdmin/list_column.html.twig',
            Column::OPTION_SORTABLE => true,
        ], $options));
        return $this;
    }

    public function addActions(array $actions, array $options = [ActionGroup::OPTION_TEMPLATE => '@EDBAdmin/button_group.html.twig']): ListCollection
    {
        $defaultTemplates = [
            'update' => '@EDBAdmin/update_button.html.twig',
            'delete' => '@EDBAdmin/delete_button.html.twig',
            'move_up' => '@EDBAdmin/move_up_button.html.twig',
            'move_down' => '@EDBAdmin/move_down_button.html.twig',
        ];

        $actionObjects = [];
        foreach ($actions as $actionName => $actionOptions) {
            if (!isset($actionOptions[Action::OPTION_TEMPLATE]) && isset($defaultTemplates[$actionName])) {
                $actionOptions[Action::OPTION_TEMPLATE] = $defaultTemplates[$actionName];
            }
            $actionObjects[] = new Action($actionName, $actionOptions);
        }

        $this->elements[] = new ActionGroup($actionObjects, $options);

        return $this;
    }

    public function getColumns(): array
    {
        return array_filter($this->elements, function ($element) {
            return $element instanceof Column;
        });
    }
}

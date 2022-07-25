<?php

declare(strict_types=1);

namespace EDB\AdminBundle\MenuBuilder;

interface MenuItemInterface
{
    public function getMenuGroup(): ?string;
    public function getMenuIcon(): string;
    public function getMenuOrderName(): ?string;
    public function getRequiredRole(): string;
    public function getRouteName(): string;
    public function getTitle(): string;
}

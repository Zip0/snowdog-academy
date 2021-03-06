<?php

namespace Snowdog\Academy\Menu;

class BorrowedBooksMenu extends AbstractMenu
{
    public function getHref(): string
    {
        return '/admin/books/list/';
    }

    public function getLabel(): string
    {
        return 'Borrowed Books';
    }

    public function isVisible(): bool
    {
        return $_SESSION['login'] && $_SESSION['is_admin'];
    }
}

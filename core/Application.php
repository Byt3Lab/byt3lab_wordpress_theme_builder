<?php

namespace Byt3lab\Builder\Core;

use Byt3lab\Builder\Admin\AdminMenu;

class Application {
    public function init() {
        if (is_admin()) {
            $adminMenu = new AdminMenu();
            $adminMenu->register();
        }
    }
}

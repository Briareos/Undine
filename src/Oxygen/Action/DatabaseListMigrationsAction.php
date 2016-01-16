<?php

namespace Undine\Oxygen\Action;

class DatabaseListMigrationsAction extends AbstractAction
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'database.listMigrations';
    }
}

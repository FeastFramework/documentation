<?php

declare(strict_types=1);

namespace Migrations;

use Feast\Database\Migration;
use Feast\Database\Table\TableFactory;

class migration5_create_files_to_releases extends Migration
{

    protected const NAME = 'Create Files to Releases';

    public function up(): void
    {
        /** @todo Create up query */
        $table = TableFactory::getTable('files_to_releases');
        $table->autoIncrement('id');
        $table->int('file_id', true);
        $table->int('release_id', true);
        $table->create();
        $this->connection->rawQuery('alter table files_to_releases add index (file_id)');
        $this->connection->rawQuery('alter table files_to_releases add index (`release_id`);');
        parent::up();
    }

    public function down(): void
    {
        /** @todo Create down query */
        $table = TableFactory::getTable('files_to_releases');
        $table->drop();
        parent::down();

    }
}
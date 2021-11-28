<?php

declare(strict_types=1);

namespace Migrations;

use Feast\Database\Migration;
use Feast\Database\Table\TableFactory;

class migration4_create_files extends Migration
{
	
	protected const NAME = 'Create Files';
	
	public function up() : void
	{
        $table = TableFactory::getTable('files');
        $table->autoIncrement('file_id');
        $table->varChar('name');
        $table->char('sha', 40);
        $table->varChar('url');
        $table->text('content');
        $table->text('html');
        $table->create();
        $this->connection->rawQuery('alter table files add index (`name`)');
        $this->connection->rawQuery('alter table files add index (`sha`);');
		parent::up();
	}
	
	public function down() : void
	{
		/** @todo Create down query */
        $table = TableFactory::getTable('files');
        $table->drop();
		parent::down();
	}
}
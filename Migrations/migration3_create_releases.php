<?php

declare(strict_types=1);

namespace Migrations;

use Feast\Database\Migration;
use Feast\Database\Table\TableFactory;

class migration3_create_releases extends Migration
{
	
	protected const NAME = 'Create Releases';
	
	public function up() : void
	{
	    /** @todo Create up query */
        $table = TableFactory::getTable('releases');
		$table->autoIncrement('release_id');
        $table->varChar('name');
        $table->varChar('tag');
        $table->varChar('url');
        $table->varChar('tar_link');
        $table->varChar('zip_link');
        $table->tinyInt('prerelease',true,1);
        $table->dateTime('created_at');
        $table->dateTime('published_at');
        $table->varChar('minor_version');
        $table->varChar('sortable_version');
        $table->create();
        $this->connection->rawQuery('alter table releases add index (minor_version)');
        $this->connection->rawQuery('alter table releases add index (sortable_version)');
        $this->connection->rawQuery('alter table releases add index (`name`);');

        parent::up();
	}
	
	public function down() : void
	{
		/** @todo Create down query */
        $table = TableFactory::getTable('releases');
        $table->drop();
        parent::down();
	}
}
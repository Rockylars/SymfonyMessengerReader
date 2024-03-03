<?php

declare(strict_types=1);

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20240219212404 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create MessengerQueue table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('messenger_queue');
        $table->addColumn('message_id', Types::INTEGER, ['notnull' => true]);
        $table->setPrimaryKey(['message_id'], 'index_primary');
        $table->addColumn('envelope', Types::TEXT, ['notnull' => true]);
        $table->addColumn('queue_name', Types::STRING, ['length' => '64', 'notnull' => true]);
        $table->addColumn('created_on', Types::DATETIME_MUTABLE, ['notnull' => true]);
        $table->addColumn('last_tried', Types::DATETIME_MUTABLE, ['notnull' => false, 'default' => null]);
        $table->addColumn('finished_at', Types::DATETIME_MUTABLE, ['notnull' => false, 'default' => null]);
        $table->addcolumn('status', Types::STRING, ['notnull' => true, 'length' => 10]);
        $table->addcolumn('message_name', Types::STRING, ['notnull' => true, 'length' => 100]);
        $table->addColumn('error_message', Types::TEXT, ['notnull' => false, 'default' => null]);
        $table->addColumn('key_identifier', Types::STRING, ['notnull' => false, 'default' => null, 'length' => 300]);
        $table->addIndex(['message_id', 'queue_name', 'status'], 'index_search');
    }

    public function down(Schema $schema): void
    {
        // Make a new migration.
    }
}

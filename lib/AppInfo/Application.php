<?php

declare(strict_types=1);

namespace OCA\AeneasDispensary\AppInfo;

use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\IDBConnection;

class Application extends App implements IBootstrap {
    public const APP_ID = 'aeneas_dispensary';

    public function __construct(array $urlParams = []) {
        parent::__construct(self::APP_ID, $urlParams);
    }

    public function register(IRegistrationContext $context): void {
        // leer
    }

    public function boot(IBootContext $context): void {
        // Force-Check der Tabelle beim Booten
        $connection = \OC::$server->get(IDBConnection::class);
        $schema = $connection->createSchemaManager();
        
        if (!$schema->tablesExist(['*PREFIX*aeneas_abgabe'])) {
            $this->createTable($connection);
        }
    }

    private function createTable(IDBConnection $connection): void {
        $schema = $connection->createSchema();
        $table = $schema->createTable($connection->getPrefix() . 'aeneas_abgabe');
        $table->addColumn('id', 'integer', ['autoincrement' => true, 'notnull' => true]);
        $table->addColumn('user_id', 'string', ['notnull' => true, 'length' => 64]);
        $table->addColumn('amount', 'integer', ['notnull' => true]);
        $table->addColumn('timestamp', 'integer', ['notnull' => true]);
        $table->addColumn('edited_by', 'string', ['notnull' => false, 'length' => 64]);
        $table->addColumn('edited_at', 'integer', ['notnull' => false]);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['user_id'], 'abgabe_user_idx');
        
        foreach ($schema->toSql($connection->getDatabasePlatform()) as $sql) {
            $connection->executeQuery($sql);
        }
    }
}
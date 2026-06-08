<?php

declare(strict_types=1);

namespace OCA\AeneasDispensary\Migration;

use OCP\DB\ISchemaWrapper;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

class Version010002Date20260608000000 extends SimpleMigrationStep {

    public function changeSchema(IOutput $output, \Closure $schemaClosure, array $options): void {
        /** @var ISchemaWrapper $schema */
        $schema = $schemaClosure();

        if (!$schema->hasTable('aeneas_abgabe')) {
            $table = $schema->createTable('aeneas_abgabe');
            
            $table->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true,
            ]);
            
            $table->addColumn('user_id', 'string', [
                'notnull' => true,
                'length' => 64,
            ]);
            
            $table->addColumn('amount', 'integer', [
                'notnull' => true,
            ]);
            
            $table->addColumn('timestamp', 'integer', [
                'notnull' => true,
            ]);
            
            $table->addColumn('edited_by', 'string', [
                'notnull' => false,
                'length' => 64,
            ]);
            
            $table->addColumn('edited_at', 'integer', [
                'notnull' => false,
            ]);

            $table->setPrimaryKey(['id']);
            $table->addIndex(['user_id'], 'abgabe_user_idx');
        }
    }
}
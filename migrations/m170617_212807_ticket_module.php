<?php
//namespace bausch\ticket\migrations\modules; 
//use Yii;
use yii\db\Migration;
use yii\helpers\Json;

class m170617_212807_ticket_module extends Migration
{
    /**
     * yii migrate      --migrationPath=frontend/modules/ticket/migrations --db=db_ticket
     * yii migrate/down --migrationPath=frontend/modules/ticket/migrations --db=db_ticket
     * 
     * 
     */
    public function init()
    {
        $this->db = 'db_ticket';
		parent::init();
    }
    
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            echo 'Using mysql driver -----------------------------------';
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
         
        $this->createTable('{{%ticket}}', [
            'ticket_id_pk' => $this->primaryKey(), //Schema::TYPE_PK,
            'process' => $this->string()->notNull(),
            'as_reported' => $this->text()->notNull(),
            'as_determined' => $this->text(),
            'applied_fix' => $this->text(),
            'canceled_reason' => $this->text(),
            'machine_status' => "enum('Up', 'Down', 'N/A') DEFAULT 'Down'",
            'ticket_status' => "enum('Open','In Repair','Closed','Canceled') DEFAULT 'Open'",
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'created_by' => $this->string(30)->notNull(),
            'taken_at' => $this->timestamp()->defaultValue(null),
            'taken_by' => $this->string(30),
            'closed_at' => $this->timestamp()->defaultValue(null),
            'closed_by' => $this->string(30),
            'canceled_at' => $this->timestamp()->defaultValue(null),
            'canceled_by' => $this->string(30),
        ], $tableOptions);
        
        $this->createTable('{{%ticket_data}}', [
            'ticket_data_id_pk' => $this->primaryKey(), //Schema::TYPE_PK,
            'ticket_id_fk' => $this->integer(),
            'category' => $this->string(30)->notNull(),
            'obj' => $this->string()->notNull(),
            'val' => $this->string()->notNull(),
            'extra' => $this->string()->defaultValue(null),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'created_by' => $this->string(30)->notNull(),
        ], $tableOptions);
        
        $this->createTable('{{%workflow}}', [
            'workflow_id_pk' => $this->primaryKey(), //Schema::TYPE_PK,
            'workflow' => $this->string()->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'created_by' => $this->string(30)->notNull(),
            'updated_at' => $this->timestamp()->defaultValue(null),
            'updated_by' => $this->string(30),
        ], $tableOptions);
        
        $this->createTable('{{%step}}', [
            'step_id_pk' => $this->primaryKey(), //Schema::TYPE_PK,
            'workflow_id_fk' => $this->integer(), //Schema::TYPE_PK,
            'category' => $this->string(30)->notNull(),
            'step' => $this->string()->notNull(),
            'step_type' => "enum('Start','Flow','End') DEFAULT 'Flow'",
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'created_by' => $this->string(30)->notNull(),
            'updated_at' => $this->timestamp()->defaultValue(null),
            'updated_by' => $this->string(30),
        ], $tableOptions);
        
        $this->createTable('{{%item}}', [
            'item_id_pk' => $this->primaryKey(), //Schema::TYPE_PK,
            'step_id_fk' => $this->integer(), //Schema::TYPE_PK,
            'item' => $this->string()->notNull(),
            'to_step_id_fk' => $this->string(),
            'append_input' => $this->integer(1)->defaultValue('0'),
            'append_input' => $this->string(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'created_by' => $this->string(30)->notNull(),
            'updated_at' => $this->timestamp()->defaultValue(null),
            'updated_by' => $this->string(30),
        ], $tableOptions);
        
        //Import initial configuration.
		echo "    > Loading tables with configuration\n";
		$dir = Yii::getAlias('@vendor/bausch/yii2-ticket/migrations/_data/');
		foreach (glob($dir . '*.json') as $filename) {
			$table = basename($filename, '.json');
			echo "      - Table: " . $table . "\n";
			// Ensure table exists.
			$tableSchema = Yii::$app->db_ticket->schema->getTableSchema($table);
			//If table does not exist:
			if ($tableSchema === null) {
				echo "      - Table $table does not exist. The filename must be the table name.\n";
			} else {
				//Otherwise, table exists do a quick file check.
				$file = file_get_contents($filename);
				$lines = substr_count($file, "\n");
				if ($lines > 0) {
					echo "      - Records created: " . $lines . "\n";
					$data_r = Json::decode($file, true);
					$columns = array_keys($data_r[0]);
					$data = array_values($data_r);
					Yii::$app->db_ticket->createCommand()->batchInsert($table, $columns, $data)->execute();
				} else {
					echo '**    - No data to process for this file.\n';
				}
			} //End of if table exists.
        }
        
        // ticket_data table
        $this->createIndex('idx_ticket_data_id_pk', 'ticket_data', ['ticket_data_id_pk'], true);
        $this->createIndex('idx_ticket_id_fk', 'ticket_data', ['ticket_id_fk'], false);
        $this->addForeignKey('fk_ticket_id', 'ticket_data', 'ticket_id_fk', 'ticket', 'ticket_id_pk', 'CASCADE', 'CASCADE');
        
        // step table
        $this->createIndex('idx_step_id_pk',    'step', ['step_id_pk'], true);
        $this->createIndex('idx_unique_wf_step','step', ['workflow_id_fk', 'step'], true);
        $this->addForeignKey('fk_workflow_id', 'step', 'workflow_id_fk', 'workflow', 'workflow_id_pk', 'CASCADE', 'CASCADE');
        
        // item table
        $this->createIndex('idx_item_id_pk', 'item', ['item_id_pk'], true);
        $this->createIndex('idx_unique_step_item', 'item', ['step_id_fk', 'item'], true);
        $this->addForeignKey('fk_step_id', 'item', 'step_id_fk', 'step', 'step_id_pk', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_ticket_id', 'ticket_data');
        $this->dropForeignKey('fk_workflow_id', 'step');
        $this->dropForeignKey('fk_step_id', 'item');
        
        $this->dropIndex('idx_ticket_data_id_pk', 'ticket_data');
        $this->dropIndex('idx_ticket_id_fk', 'ticket_data');
        $this->dropIndex('idx_step_id_pk', 'step');
        $this->dropIndex('idx_unique_wf_step', 'step');
        $this->dropIndex('idx_item_id_pk', 'item');
        $this->dropIndex('idx_unique_step_item', 'item');
        
        $this->dropTable('{{%ticket}}');
        $this->dropTable('{{%ticket_data}}');
        $this->dropTable('{{%workflow}}');
        $this->dropTable('{{%step}}');
        $this->dropTable('{{%item}}');
        
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
    
    }
    */
         
    /*
    public function down()
    {
        echo "m170617_212807_ticket_module cannot be reverted.\n";
        return false;
    }
    */
}

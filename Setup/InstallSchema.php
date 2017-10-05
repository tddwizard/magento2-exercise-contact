<?php


namespace TddWizard\ExerciseContact\Setup;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\InstallSchemaInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        $table_tddwizard_inquiry = $setup->getConnection()->newTable($setup->getTable('tddwizard_inquiry'));

        
        $table_tddwizard_inquiry->addColumn(
            'inquiry_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,),
            'Entity ID'
        );
        

        
        $table_tddwizard_inquiry->addColumn(
            'email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'email'
        );
        

        
        $table_tddwizard_inquiry->addColumn(
            'message',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'message'
        );
        

        $setup->getConnection()->createTable($table_tddwizard_inquiry);

        $setup->endSetup();
    }
}

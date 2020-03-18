<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */


namespace Amasty\Orderattr\Setup\Operation;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;
use Amasty\Orderattr\Api\Data\CheckoutAttributeInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

class CreateEavAttributeStoreTable
{
    const TABLE_NAME = 'amasty_order_attribute_eav_attribute_store';

    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->createTable(
            $this->createTable($setup)
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     *
     * @return Table
     */
    private function createTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getTable(self::TABLE_NAME);
        $attributeTable = $setup->getTable('eav_attribute');
        $storeTable = $setup->getTable('store');

        return $setup->getConnection()
            ->newTable(
                $table
            )->setComment(
                'Amasty Order Attribute Store Relation table'
            )->addColumn(
                CheckoutAttributeInterface::ATTRIBUTE_ID,
                Table::TYPE_SMALLINT,
                null,
                [
                    'nullable' => false, 'default' => 0, 'unsigned' => true
                ],
                'Order Attribute Entity ID'
            )->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true, 'nullable' => false
                ],
                'Store Id'
            )->addIndex(
                $setup->getIdxName(
                    $table,
                    [CheckoutAttributeInterface::ATTRIBUTE_ID, 'store_id'],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                [CheckoutAttributeInterface::ATTRIBUTE_ID, 'store_id'],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )->addForeignKey(
                $setup->getFkName(
                    $table,
                    CheckoutAttributeInterface::ATTRIBUTE_ID,
                    $attributeTable,
                    CheckoutAttributeInterface::ATTRIBUTE_ID
                ),
                CheckoutAttributeInterface::ATTRIBUTE_ID,
                $attributeTable,
                CheckoutAttributeInterface::ATTRIBUTE_ID,
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    $table,
                    'store_id',
                    $storeTable,
                    'store_id'
                ),
                'store_id',
                $storeTable,
                'store_id',
                Table::ACTION_CASCADE
            );
    }
}

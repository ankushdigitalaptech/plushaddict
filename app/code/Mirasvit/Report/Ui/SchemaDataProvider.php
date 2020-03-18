<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-report
 * @version   1.3.87
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Report\Ui;

use Magento\Backend\Block\Template;
use Magento\Framework\Profiler;
use Mirasvit\ReportApi\Api\SchemaInterface;

class SchemaDataProvider extends Template
{
    private $schema;

    public function __construct(
        SchemaInterface $schema,
        Template\Context $context
    ) {
        $this->schema = $schema;

        parent::__construct($context);
    }

    public function getConfigData()
    {
        Profiler::start(__METHOD__);

        $result = [
            'tables'  => [],
            'columns' => [],
        ];

        foreach ($this->schema->getTables() as $table) {
            $result['tables'][$table->getName()] = [
                'identifier' => $table->getName(),
                'label'      => $table->getLabel() ? $table->getLabel() : $table->getName(),
                'internal'   => $table->getLabel() ? false : true,
            ];

            foreach ($table->getColumns() as $column) {
                $group = '';
                foreach ($column->getFields() as $field) {
                    $group .= $field->getName();
                }

                $result['columns'][$column->getIdentifier()] = [
                    'label'      => $column->getLabel(),
                    'group'      => $group,
                    'identifier' => $column->getIdentifier(),
                    'internal'   => $column->isInternal(),
                    'table'      => $column->getTable()->getName(),
                    'type'       => $column->getType()->getType(),
                    'filterType' => $column->getType()->getJsFilterType(),
                    'aggregator' => $column->getAggregator()->getType(),
                    'options'    => [],
                ];

                try {
                    $result['columns'][$column->getIdentifier()]['options'] = method_exists($column->getType(), 'getOptions')
                        ? $column->getType()->getOptions()
                        : [];
                } catch (\Exception $e) {
                }
            }
        }

        Profiler::stop(__METHOD__);

        return $result;
    }

    public function toHtml()
    {
        try {
            $json = \Zend_Json::encode($this->getConfigData());
        } catch (\Exception $e) {
            return "<div class='message message-error'>" . $e->getMessage() . "</div>";
        }

        return "<script>var schemaDataProvider = $json</script>";
    }
}

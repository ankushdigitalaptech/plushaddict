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
 * @package   mirasvit/module-search
 * @version   1.0.141
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Search\Model\ScoreRule;

use Magento\Rule\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Model\ProductFactory as ProductFactory;
use Magento\Framework\Model\ResourceModel\Iterator;
use Mirasvit\Core\Service\CompatibilityService;
use Mirasvit\Search\Helper\Serializer;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

class Rule extends AbstractModel
{
    const FORM_NAME = 'search_scorerule_form';

    /**
     * @var PostCondition\CombineFactory
     */
    private $postConditonCombineFactory;

    /**
     * @var Condition\CombineFactory
     */
    private $conditionCombineFactory;

    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * @var Iterator
     */
    private $iterator;

    /**
     * @var array
     */
    private $productIds = [];

    /**
     * @var Serializer
     */
    protected $serializer;

    /** @var \Magento\Rule\Model\Condition\Combine */
    private $_postConditions;

    /**
     * Rule constructor.
     * @param PostCondition\CombineFactory $postConditionCombineFactory
     * @param Condition\CombineFactory $conditionCombineFactory
     * @param ProductCollectionFactory $productCollectionFactory
     * @param ProductFactory $productFactory
     * @param Iterator $iterator
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param TimezoneInterface $localeDate
     * @param Serializer $serializer
     */
    public function __construct(
        PostCondition\CombineFactory $postConditionCombineFactory,
        Condition\CombineFactory $conditionCombineFactory,
        ProductCollectionFactory $productCollectionFactory,
        ProductFactory $productFactory,
        Iterator $iterator,
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        TimezoneInterface $localeDate,
        Serializer $serializer
    ) {
        $this->postConditonCombineFactory = $postConditionCombineFactory;
        $this->conditionCombineFactory = $conditionCombineFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productFactory = $productFactory;
        $this->iterator = $iterator;
        $this->serializer = $serializer;

        parent::__construct($context, $registry, $formFactory, $localeDate);
    }

    /**
     * {@inheritdoc}
     */
    public function getActionsInstance()
    {
        return $this->postConditonCombineFactory->create();
    }

    /**
     * @return \Magento\Rule\Model\Condition\Combine|PostCondition\Combine
     */
    public function getPostConditionsInstance()
    {
        return $this->postConditonCombineFactory->create();
    }

    /**
     * @return \Magento\Rule\Model\Condition\Combine|Condition\Combine
     */
    public function getConditionsInstance()
    {
        return $this->conditionCombineFactory->create();
    }

    /**
     * Retrieve rule combine conditions model
     *
     * @return \Magento\Rule\Model\Condition\Combine
     */
    public function getPostConditions()
    {
        if (empty($this->_postConditions)) {
            $this->_resetPostConditions();
        }

        // Load rule conditions if it is applicable
        if ($this->hasPostConditionsSerialized()) {
            $conditions = $this->getPostConditionsSerialized();
            if (!empty($conditions)) {
                if (CompatibilityService::is21()) {
                    $conditions = $this->serializer->unserialize($conditions);
                } else {
                    $conditions = \Zend_Json::decode($conditions);
                }
                if (is_array($conditions) && !empty($conditions)) {
                    $this->_postConditions->loadArray($conditions);
                }
            }
            $this->unsPostConditionsSerialized();
        }

        return $this->_postConditions;
    }

    /**
     * Reset rule combine conditions
     *
     * @param null|\Magento\Rule\Model\Condition\Combine $conditions
     * @return $this
     */
    protected function _resetPostConditions($conditions = null)
    {
        if (null === $conditions) {
            $conditions = $this->getPostConditionsInstance();
        }
        $conditions->setRule($this)->setId('1');
        $this->setPostConditions($conditions);

        return $this;
    }

    /**
     * Set rule combine conditions model
     *
     * @param \Magento\Rule\Model\Condition\Combine $conditions
     * @return $this
     */
    public function setPostConditions($conditions)
    {
        $this->_postConditions = $conditions;
        return $this;
    }

    /**
     * Get array of product ids which are matched by rule
     * @param array $ids
     * @return array
     */
    public function getMatchingProductIds(array $ids)
    {
        $productCollection = $this->productCollectionFactory->create();

        if (count($ids)) {
            $productCollection->addFieldToFilter('entity_id', $ids);
        }

        $this->getConditions()->collectValidatedAttributes($productCollection);

        $this->iterator->walk(
            $productCollection->getSelect(),
            [[$this, 'callbackValidateProduct']],
            [
                'attributes' => $this->getCollectedAttributes(),
                'product'    => $this->productFactory->create(),
            ]
        );

        return $this->productIds;
    }

    /**
     * Callback function for product matching
     *
     * @param array $args
     * @return void
     */
    public function callbackValidateProduct($args)
    {
        $product = clone $args['product'];
        $product->setData($args['row']);

        $product->setData('product', $product);

        $product->setStoreId(1);
        $product->load($product->getId());

        if ($this->getConditions()->validate($product)) {
            $this->productIds[] = $product->getId();
        }
    }
}

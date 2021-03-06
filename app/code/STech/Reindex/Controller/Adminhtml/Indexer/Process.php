<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace STech\Reindex\Controller\Adminhtml\Indexer;

use Magento\Backend\App\Action\Context;

class Process extends \Magento\Indexer\Controller\Adminhtml\Indexer
{
    /** @var \Magento\Framework\Indexer\IndexerInterface  */
    protected $indexerFactory;

    /**
     * Index constructor.
     * @param Context $context
     * @param \Magento\Indexer\Model\IndexerFactory $indexerFactory
     */
    public function __construct(
        Context $context,
        \Magento\Indexer\Model\IndexerFactory $indexerFactory
    ) {
        $this->indexerFactory = $indexerFactory;
        parent::__construct($context);
    }
    
    /**
     * Turn mview on for the given indexers
     *
     * @return void
     */
    protected function _isAllowed()
    {
        return true;
    }

    public function execute()
    {
        $indexerId = $this->getRequest()->getParam('indexer_id');
        try {
            $indexer = $this->indexerFactory->create();
            $indexer->load($indexerId)->reindexAll();
            $this->messageManager->addSuccess(
                __($indexer->getTitle().' index was rebuilt.')
            );
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException(
                $e,
                __("We couldn't reindex because of an error.")
            );
        }
        $this->_redirect('indexer/indexer/list');
    }
}

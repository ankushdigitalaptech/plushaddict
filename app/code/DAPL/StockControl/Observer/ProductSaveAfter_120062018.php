<?php
namespace DAPL\StockControl\Observer;
 
use \Psr\Log\LoggerInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use DAPL\StockControl\Helper\Data as StockControlHelper;

 
class ProductSaveAfter implements ObserverInterface
{
    
    protected $logger;

    protected $convertOrder;    

    private $_objectManager;

    private $stockcontrolhelper;
   
    public function __construct(
        LoggerInterface $logger,
        \Magento\Sales\Model\Convert\Order $convertOrder,
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        StockControlHelper $stockcontrolhelper
        )
    {
        $this->logger = $logger;
        $this->convertOrder = $convertOrder;
        $this->_objectManager = $objectmanager;
        $this->stockcontrolhelper = $stockcontrolhelper;
    }
 
    public function execute(Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        if($product){
            $this->stockcontrolhelper->processSavedProduct($product);
        }
    }
}
<?php
namespace Dapreport\Wholesalereport\Block\Adminhtml\Wholesalereport;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Store\Model\StoreManagerInterface;

class Reorderinput extends AbstractRenderer
{
   private $_storeManager;
   /**
    * @param \Magento\Backend\Block\Context $context
    * @param array $data
    */
    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $data = []
    ) {
        $this->_storeManager = $storeManager;
      }

    public function render(\Magento\Framework\DataObject $row)
    {
       
            return "<input type='text' name='reorderinput_custom' class='reorderInputCustom' data-pid='".$row->getId()."' />";
        
    }
	
}
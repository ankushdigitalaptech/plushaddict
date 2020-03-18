<?php
namespace DAPL\Banner\Block\Adminhtml;
class Banner extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
		
        $this->_controller = 'adminhtml_banner';/*block grid.php directory*/
        $this->_blockGroup = 'DAPL_Banner';
        $this->_headerText = __('Banner');
        $this->_addButtonLabel = __('Add New Entry'); 
        parent::_construct();
		
    }
}

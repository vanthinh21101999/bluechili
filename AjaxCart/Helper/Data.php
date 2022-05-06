<?php
namespace BlueChili\AjaxCart\Helper;

use BlueChili\AjaxCart\Model\Source\AnimationType as AnimationType;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    // Define variable global
    const XML_PATH_ENABLE = 'ajaxcart/general/enable';
    const PRODUCT_TYPE_SIMPLE = 'simple';
    const XML_PATH_ANIMATION_TYPE = 'ajaxcart/additional/animation_type';

    /**
     * @var BlueChili\AjaxCart\Helper\Data
     */
    protected $aHelper;
    
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    
    protected $_storeManager;
    /**
     * [__construct description]
     * @param \Magento\Framework\App\Helper\Context $context[description]
     */
    
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ){
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_storeManager = $storeManager;
    }


    /**
     * Get store identifier
     *
     * @return  int
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    /*
     * return enable / disable module with magento path
     * @return string
     */
    public function isEnable()
    {
        return $this->getConfig(self::XML_PATH_ENABLE);

    }

    /*
     * return message with magento path
     * @return string
     */
    public function getConfig($path)
    {
        return $this->_scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStoreId());
    }

    public function getConfigStatic($path)
    {
        return $this->_scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStoreId());
    }

    /**
     * Check type product
     * @return boolean
     */
    public function checkTypeProduct($product)
    {
        if($product->getTypeId() == self::PRODUCT_TYPE_SIMPLE) {
            return true;
        }
        return false;
    }

    /**
     * Check isSiderbarCart
     * @return boolean
     */
    public function checkSiderbarCart()
    {
        if($this->getConfig(self::XML_PATH_ANIMATION_TYPE) == AnimationType::TYPE_SIDEBAR_CART) {
            return true;
        }
        return false;
    }

    /**
     * Check apply type ajaxcart for product.
     * @return boolean
     */
    public function checkApplySidebarCart($product)
    {
        if($this->checkSiderbarCart() && $this->checkTypeProduct($product) == false) {
            return true;
        }
        return false;
    }
    

}
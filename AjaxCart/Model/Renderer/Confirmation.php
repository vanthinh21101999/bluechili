<?php
namespace BlueChili\AjaxCart\Model\Renderer;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Element\Template;
use BlueChili\AjaxCart\Helper\Data as aHelper;

/**
 * Class Confirmation
 * @package BlueChili\AjaxCart\Model\Renderer
 */
class Confirmation extends AbstractRenderer
{
    const XML_PATH_CONTINUE = 'ajaxcart/additional/continue';
    const XML_PATH_REDIRECT_CHECKOUT_PAGE = 'ajaxcart/additional/redirect_checkout_page';
    const XML_PATH_REDIRECT_CART_PAGE = 'ajaxcart/additional/redirect_cart_page';
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var BlueChili\AjaxCart\Helper\Data
     */
    protected $aHelper;

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Checkout\Model\Session $checkoutSession,
        PriceCurrencyInterface $priceCurrency,
        aHelper $aHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($scopeConfig);
        $this->request = $request;
        $this->productRepository = $productRepository;
        $this->checkoutSession = $checkoutSession;
        $this->priceCurrency = $priceCurrency;
        $this->aHelper = $aHelper;
    }

    /**
     * @inheritdoc
     */
    public function render($layout)
    {
        $is_continue = false;
        $is_redirect_checkout = false;
        $is_redirect_cart = false;
        if($this->aHelper->getConfig(self::XML_PATH_CONTINUE)){
            $is_continue = true;
        }
        if($this->aHelper->getConfig(self::XML_PATH_REDIRECT_CHECKOUT_PAGE)){
            $is_redirect_checkout = true;
        }
        if($this->aHelper->getConfig(self::XML_PATH_REDIRECT_CART_PAGE)){
            $is_redirect_cart = true;
        }
        /** @var Template $block */
        $block = $layout->createBlock(
            Template::class,
            'ajaxcart.ui.confirmation',
            [
                'data' => [
                    'cart_subtotal' => $this->getCartSubtotal(),
                    'cart_summary' => $this->getCartSummary(),
                    'is_continue'  => $is_continue,
                    'is_checkout'  => $is_redirect_checkout,
                    'is_cart'      => $is_redirect_cart
                ]
            ]
        );
        $this
            ->appendProductImage($block, $layout, $this->getProduct());
        return $block
            ->setTemplate('BlueChili_AjaxCart::ui/confirmation.phtml')
            ->setProductName($this->getProduct()->getName())
            ->setProductQty($this->getCartQty())
            ->toHtml();
    }

    /**
     * Get product
     *
     * @return \Magento\Catalog\Model\Product |bool
     */
    private function getProduct()
    {
        $productId = (int)$this->request->getParam('product');
        if ($productId) {
            return $this->productRepository->getById($productId);
        }
        return false;
    }

    /**
     * Get quote
     *
     * @return \Magento\Quote\Model\Quote
     */
    private function getQuote()
    {
        return $this->checkoutSession->getQuote();
    }

    /**
     * Get cart subtotal
     *
     * @return string
     */
    private function getCartSubtotal()
    {
        $totals = $this->getQuote()->getTotals();
        return $this->priceCurrency->format(
            isset($totals['subtotal']) ? $totals['subtotal']->getValue() : 0,
            true,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            $this->getQuote()->getStore()
        );
    }

    /**
     * Get cart summary
     *
     * @return float|int|null
     */
    private function getCartSummary()
    {
        $useQty = $this->scopeConfig->getValue(
            'checkout/cart_link/use_qty',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $useQty
            ? $this->getQuote()->getItemsQty()
            : $this->getQuote()->getItemsCount();
    }
    /**
     * Get cart qty
     *
     * @return float|int|null
     */
    private function getCartQty()
    {
        $productQty = (int)$this->request->getParam('qty');
        if ($productQty == '') {
            $productQty = 1;
        }
        return $productQty;
    }
}

<?php
namespace Zou\HelloWorld\Helper;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Customer\Model\Context;
class Data extends \Magento\Framework\App\Helper\AbstractHelper{
    protected $_storeManager;
    protected $_responseFactory;
    protected $_url;
    protected $_customerSession;
    protected $_objectManager;
    protected $_checkoutSession;
    protected $_transportBuilder;
    protected $_inlineTranslation;
    protected $httpContext;
    protected $_categoryCollectionFactory;
    protected $_category;
    protected $_coreRegistry;
    protected $_product = null;
    protected $_customerUrl;
    public function __construct(
        \Magento\Framework\App\Helper\Context  $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryFactory,
        \Magento\Catalog\Model\Category $category,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Framework\Registry $registry
        ) {
            
            parent::__construct($context);
            $this->_responseFactory = $responseFactory;
            $this->_url = $context->getUrlBuilder();
            $this->_customerSession = $customerSession;
            $this->scopeConfig = $context->getScopeConfig();
            $this->_objectManager = $objectManager;
            $this->_checkoutSession = $checkoutSession;
            $this->_transportBuilder = $transportBuilder;
            $this->_inlineTranslation = $inlineTranslation;
            $this->httpContext = $httpContext;
            $this->_storeManager = $storeManager;
            $this->_categoryCollectionFactory = $categoryFactory;
            $this->_category = $category;
            $this->_coreRegistry = $registry;
            $this->_customerUrl = $customerUrl;
    }
    function getGeneralCategorys(){
        $store = $this->_storeManager->getStore();
        $collection = $this->_category->getCollection()
        ->addFieldToFilter('is_active', 1)
        ->addFieldToFilter('parent_id', 1)
        ->addStoreFilter($store)
        ->setOrder("cat_position", "ASC");
        return $collection;
    }
    public function getBaseUrlMedia($path = '', $secure = false)
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA, $secure) . $path;
    }
    
    public function show404(){
        $url = $this->_url->getUrl('noroute');
        $this->_responseFactory->create()->setRedirect($url)->sendResponse();
        exit();
    }
    
    
    public function isLoggedIn()
    {
        return $this->httpContext->getValue(Context::CONTEXT_AUTH);
    }
    
    /**
     * Get store config
     *
     * @param string $config
     */
    public function getConfig($config)
    {
        return $this->scopeConfig->getValue($config, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
    public function getFrontpageImage($file){
        return $this->_storeManager->getStore()->getBaseUrl('media').'category/tmp/frontpage_image/'.$file;
    }
    
    public function getCateById($cateId)
    {
        return $this->_category->load($cateId);
    }
    public function getParentCategories($category){
        $pathIds = array_reverse(explode(',', $category->getPathInStore()));
        /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $categories */
        $categories = $this->_categoryCollectionFactory->create();
        return $categories->setStore($this->_storeManager->getStore())
        ->addAttributeToSelect('name')
        ->addAttributeToSelect('url_key')
        ->addAttributeToSelect('image')
        ->addFieldToFilter('entity_id',['in' => $pathIds])
        ->addFieldToFilter('is_active',1)
        ->load()->getItems();
    }
    
    public function getProductCategories($cids){
        $categories = $this->_categoryCollectionFactory->create();
        return $categories->setStore($this->_storeManager->getStore())
        ->addAttributeToSelect('name')
        ->addAttributeToSelect('url_key')
        ->addAttributeToSelect('image')
        ->addFieldToFilter('entity_id',['in' => $cids])
        ->addFieldToFilter('is_active',1)
        ->setOrder('entity_id', 'desc')
        ->load()->getItems();
    }
    public function getCurrentProduct()
    {
        if (!$this->_product) {
            $this->_product = $this->_coreRegistry->registry('product');
        }
        return $this->_product;
    }
    
    public function getLogoutUrl(){
        return $this->_customerUrl->getLogoutUrl();
    }
    public function getAccountUrl(){
        return $this->_customerUrl->getAccountUrl();
    }
    public function getLoginUrl(){
        return $this->_customerUrl->getLoginUrl();
    }
}
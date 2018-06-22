<?php


namespace CustomWidgets\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class ProductsByCategory extends Template implements BlockInterface
{

    protected $_template = "widget/products-by-category.phtml";

    protected $categoryId;
    protected $limit;

    protected $categoryFactory;

    protected $_storeManager;
    protected $_appEmulation;
    protected $_blockFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Element\BlockFactory $blockFactory,
        \Magento\Store\Model\App\Emulation $appEmulation
    )
    {
        $this->_storeManager = $storeManager;
        $this->_blockFactory = $blockFactory;
        $this->_appEmulation = $appEmulation;

        $this->categoryFactory = $categoryFactory;
        parent::__construct($context);
    }

    public function getCategoryProduct()
    {
        $category = $this->categoryFactory->create()
            ->load($this->categoryId)
            ->getProductCollection()
            ->addAttributeToSelect('*');

        if (!empty($this->limit)) {
            $category->setPageSize($this->limit);
        }

        return $category;
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    public function setCategory($category)
    {
        $this->categoryId = $category;
        return $this;
    }

    public function getCategory()
    {
        return $this->categoryId;
    }

    public function getImageUrl($product, $imageType = '')
    {
        $storeId = $this->_storeManager->getStore()->getId();

        $this->_appEmulation->startEnvironmentEmulation($storeId, \Magento\Framework\App\Area::AREA_FRONTEND, true);

        $imageBlock   = $this->_blockFactory->createBlock('Magento\Catalog\Block\Product\ListProduct');
        $productImage = $imageBlock->getImage($product, $imageType);

        $this->_appEmulation->stopEnvironmentEmulation();

        return $productImage->toHtml();
    }


}
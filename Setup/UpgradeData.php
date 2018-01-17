<?php
namespace Iggo\Fenzsofttestdemo\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
// use Magento\Framework\Setup\SchemaSetupInterface;

use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Setup\SalesSetupFactory;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
/**
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    protected $salesSetupFactory;
    /**
     * @var CustomerSetupFactory
     */
    protected $customerSetupFactory;
    protected $schemaSetup;
    private $categorySetupFactory;
    private $eavSetupFactory;
    private $attributeSetFactory;
    protected $quoteSetupFactory;
    public function __construct(
        SalesSetupFactory $salesSetupFactory,
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory,
        CategorySetupFactory $categorySetupFactory,
        EavSetupFactory $eavSetupFactory,
        QuoteSetupFactory $quoteSetupFactory
        //         SchemaSetupInterface $schemaSetup
        ) {
            $this->customerSetupFactory = $customerSetupFactory;
            $this->attributeSetFactory = $attributeSetFactory;
            $this->categorySetupFactory = $categorySetupFactory;
            $this->eavSetupFactory = $eavSetupFactory;
            $this->quoteSetupFactory = $quoteSetupFactory;
            $this->salesSetupFactory = $salesSetupFactory;
            //             $this->schemaSetup = $schemaSetup;
    }
    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '0.1.1', '<')) {
            // $attribuets = array(
            //     'subtitle' => array(
            //         'type' => 'varchar',
            //         'label' => 'Subtitle',
            //         'input' => 'text',
            //     ),
            //     'colours_and_materials' => array(
            //         'type' => 'text',
            //         'label' => 'Colours And Materials',
            //         'input' => 'textarea',
            //         'wysiwyg_enabled'=>true,
            //         'group'=>'Content'
            //     ),
            //     'dimensions' => array(
            //         'type' => 'text',
            //         'label' => 'Dimensions',
            //         'input' => 'textarea',
            //         'wysiwyg_enabled'=>true,
            //         'group'=>'Content'
            //     ),
            //     'features' => array(
            //         'type' => 'text',
            //         'label' => 'Features',
            //         'input' => 'textarea',
            //         'wysiwyg_enabled'=>true,
            //         'group'=>'Content'
            //     )
            // );
            // $this->createProductAttribute($setup, $attribuets);
        }
        $setup->endSetup();
    }
    
    function createProductAttribute($setup,$attribuets) {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $aScope = \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL;
        foreach ($attribuets as $code=>$_attribute){
            if(isset($_attribute['type']) &&  $_attribute['type']
                && isset($_attribute['input']) &&  $_attribute['input']
                && isset($_attribute['label']) &&  $_attribute['label']
                ){
                    $input = $_attribute['input'];
                    if ($input == "boolean") {
                        $_attribute['source'] = "Magento\Eav\Model\Entity\Attribute\Source\Boolean";
                    }
                    $eavSetup->addAttribute(
                        \Magento\Catalog\Model\Product::ENTITY, $code, [
                            'group' => isset($_attribute['group'])?$_attribute['group']:'General',
                            'type' => $_attribute['type'],
                            'label' => $_attribute['label'],
                            'input' => $_attribute['input'],
                            'backend' => isset($_attribute['backend'])?$_attribute['backend']:null,
                            'required' => isset($_attribute['required'])?$_attribute['required']:false,
                            'sort_order' => isset($_attribute['sort_order'])?$_attribute['sort_order']:1000,
                            'user_defined' => isset($_attribute['user_defined'])?$_attribute['user_defined']:true,
                            'global' => isset($_attribute['global'])?$_attribute['global']:$aScope,
                            'used_in_product_listing' => true,
                            'visible' => true,
                            'source' => isset($_attribute['source'])?$_attribute['source']:'',
                            'wysiwyg_enabled'=>isset($_attribute['wysiwyg_enabled'])?$_attribute['wysiwyg_enabled']:false,
                            'default' => isset($_attribute['default'])?$_attribute['default']:null
                        ]
                        );
            }
        }
    
    }
    function createCategoryAttribute($setup,$attribuets) {
        $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
        $aScope = \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL;
        foreach ($attribuets as $code=>$_attribute){
            if(isset($_attribute['type']) &&  $_attribute['type']
                && isset($_attribute['input']) &&  $_attribute['input']
                && isset($_attribute['label']) &&  $_attribute['label']
                ){
                    $input = $_attribute['input'];
                    if ($input == "boolean") {
                        $_attribute['source'] = "Magento\Eav\Model\Entity\Attribute\Source\Boolean";
                    }
                    $categorySetup->addAttribute(
                       \Magento\Catalog\Model\Category::ENTITY, $code, [
                            'group' => isset($_attribute['group'])?$_attribute['group']:'General Information',
                            'type' => $_attribute['type'],
                            'label' => $_attribute['label'],
                            'input' => $_attribute['input'],
                            'backend' => isset($_attribute['backend'])?$_attribute['backend']:null,
                            'required' => isset($_attribute['required'])?$_attribute['required']:false,
                            'sort_order' => isset($_attribute['sort_order'])?$_attribute['sort_order']:1000,
                            'user_defined' => isset($_attribute['user_defined'])?$_attribute['user_defined']:true,
                            'global' => isset($_attribute['global'])?$_attribute['global']:$aScope,
                            'used_in_product_listing' => isset($_attribute['used_in_product_listing'])?$_attribute['used_in_product_listing']:true,
                            'is_used_in_grid' => isset($_attribute['is_used_in_grid'])?$_attribute['is_used_in_grid']:true,
                            'is_visible_in_grid' => isset($_attribute['is_visible_in_grid'])?$_attribute['is_visible_in_grid']:false,
                            'is_filterable_in_grid' => isset($_attribute['is_filterable_in_grid'])?$_attribute['is_filterable_in_grid']:true,
                            'visible' => true,
                            'source' => isset($_attribute['source'])?$_attribute['source']:'',
                            'wysiwyg_enabled'=>isset($_attribute['wysiwyg_enabled'])?$_attribute['wysiwyg_enabled']:false,
                            'default' => isset($_attribute['default'])?$_attribute['default']:null
                        ]
                        );
            }
        }
    
    }
}
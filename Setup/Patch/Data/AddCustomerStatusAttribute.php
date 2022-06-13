<?php

declare(strict_types=1);

namespace Fiodarau\CustomerStatus\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\Attribute as AttributeResource;
use Magento\Eav\Model\Config;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Psr\Log\LoggerInterface;

class AddCustomerStatusAttribute implements DataPatchInterface
{
    /**
     * String constants
     */
    public const CUSTOMER_STATUS_ATTRIBUTE_CODE = "customer_status";
    public const CUSTOMER_STATUS_ATTRIBUTE_LABEL = "Customer Status";
    public const TYPE_VARCHAR = 'varchar';
    public const TYPE_TEXT = 'text';
    public const USED_IN_FORMS = 'used_in_forms';
    public const ATTRIBUTE_SET_ID = 'attribute_set_id';
    public const ATTRIBUTE_GROUP_ID = 'attribute_group_id';

    /**
     * @var ModuleDataSetupInterface
     */
    private ModuleDataSetupInterface $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private EavSetupFactory $eavSetupFactory;

    /**
     * @var Config
     */
    private Config $eavConfig;

    /**
     * @var AttributeResource
     */
    private AttributeResource $attributeResource;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory          $eavSetupFactory,
        Config                   $eavConfig,
        AttributeResource        $attributeResource,
        LoggerInterface          $logger
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
        $this->attributeResource = $attributeResource;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        try {
            $this->AddCustomerStatusAttribute();
        } catch (LocalizedException|\Zend_Validate_Exception $e) {
            $this->logger->critical($e);
        }
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Adding customer status attribute
     *
     * @return void
     * @throws LocalizedException
     * @throws \Zend_Validate_Exception
     */
    private function AddCustomerStatusAttribute()
    {
        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->addAttribute(
            Customer::ENTITY,
            self::CUSTOMER_STATUS_ATTRIBUTE_CODE,
            [
                'type' => self::TYPE_VARCHAR,
                'label' => self::CUSTOMER_STATUS_ATTRIBUTE_LABEL,
                'input' => self::TYPE_TEXT,
                'required' => false,
                'visible' => true,
                'user_defined' => false,
                'sort_order' => 999,
                'position' => 999,
                'system' => 0
            ]
        );

        $attributeSetId = $eavSetup->getDefaultAttributeSetId(Customer::ENTITY);
        $attributeGroupId = $eavSetup->getDefaultAttributeGroupId(Customer::ENTITY);

        $customerIpAttribute = $this->eavConfig->getAttribute(
            Customer::ENTITY,
            self::CUSTOMER_STATUS_ATTRIBUTE_CODE
        );
        $customerIpAttribute->setData(self::ATTRIBUTE_SET_ID, $attributeSetId);
        $customerIpAttribute->setData(self::ATTRIBUTE_GROUP_ID, $attributeGroupId);
        $customerIpAttribute->setData(
            self::USED_IN_FORMS,
            [
                'adminhtml_checkout',
                'adminhtml_customer',
                'adminhtml_customer_address',
                'customer_account_edit',
                'customer_address_edit',
                'customer_register_address',
                'customer_account_create'
            ]
        );

        $this->attributeResource->save($customerIpAttribute);
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies(): array
    {
        return[];
    }

    /**
     * @inheritDoc
     */
    public function getAliases(): array
    {
        return[];
    }
}

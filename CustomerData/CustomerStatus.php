<?php

declare(strict_types=1);

namespace Fiodarau\CustomerStatus\CustomerData;

use Fiodarau\CustomerStatus\Setup\Patch\Data\AddCustomerStatusAttribute;
use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Customer\Helper\Session\CurrentCustomer;

class CustomerStatus implements SectionSourceInterface
{
    /**
     * @var CurrentCustomer
     */
    private CurrentCustomer $currentCustomer;

    /**
     * CustomSection constructor
     *
     * @param CurrentCustomer $currentCustomer
     */
    public function __construct(
        CurrentCustomer $currentCustomer
    ) {
        $this->currentCustomer = $currentCustomer;
    }

    /**
     * @inheritDoc
     */
    public function getSectionData(): array
    {
        if (!$this->currentCustomer->getCustomerId()) {
            return [];
        }

        $customer = $this->currentCustomer->getCustomer();

        return [
            'customerStatus' => $customer->getCustomAttribute(AddCustomerStatusAttribute::CUSTOMER_STATUS_ATTRIBUTE_CODE)
                ->getValue()
        ];
    }
}

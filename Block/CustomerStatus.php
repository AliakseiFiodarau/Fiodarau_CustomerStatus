<?php

declare(strict_types=1);

namespace Fiodarau\CustomerStatus\Block;

use Magento\Framework\View\Element\Template;
use Magento\Backend\Block\Template\Context;

class CustomerStatus extends Template
{
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getFormAction(): string
    {
        return $this->getUrl('fiodarau_customer_status/index/submit', ['_secure' => true]);
    }
}

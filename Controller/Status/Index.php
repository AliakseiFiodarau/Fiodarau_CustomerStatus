<?php

declare(strict_types=1);

namespace Fiodarau\CustomerStatus\Controller\Status;

use Fiodarau\CustomerStatus\Setup\Patch\Data\AddCustomerStatusAttribute;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class Index extends Action
{
    /**
     * @var CurrentCustomer
     */
    private CurrentCustomer $currentCustomer;

    /**
     * @var CustomerRepositoryInterface
     */
    private CustomerRepositoryInterface $customerRepository;

    /**
     * Index constructor
     *
     * @param CurrentCustomer $currentCustomer
     * @param CustomerRepositoryInterface $customerRepository
     * @param Context $context
     */
    public function __construct(
        CurrentCustomer $currentCustomer,
        CustomerRepositoryInterface $customerRepository,
        Context $context
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->customerRepository = $customerRepository;
        parent::__construct($context);
    }

    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function execute()
    {
        $post = (array) $this->getRequest()->getPost();
        if (!empty($post)) {
            $status = $post["status"];
            $customerId = $this->currentCustomer->getCustomerId();
            $customer = $this->customerRepository->getById($customerId);
            $customer->setCustomAttribute(
                AddCustomerStatusAttribute::CUSTOMER_STATUS_ATTRIBUTE_CODE,
                $status
            );
            $this->customerRepository->save($customer);
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl('/status/status/index');
            return $resultRedirect;
        }

        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}

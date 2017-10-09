<?php

namespace TddWizard\ExerciseContact\Block;

use Magento\Framework\View\Element\Template;
use TddWizard\ExerciseContact\Model\Session;
use Magento\Customer\Model\Session as CustomerSession;

class Form extends Template
{
    /**
     * @var Session
     */
    private $session;
    /**
     * @var CustomerSession
     */
    private $customerSession;

    public function __construct(
        Template\Context $context,
        Session $session,
        CustomerSession $customerSession,
        array $data = [])
    {
        parent::__construct($context, $data);
        $this->session = $session;
        $this->customerSession = $customerSession;
    }

    public function getSavedFormData(): array
    {
        return $this->session->getSavedFormData();
    }

    public function isCustomerLoggedIn() : bool
    {
        return $this->customerSession->isLoggedIn();
    }
}
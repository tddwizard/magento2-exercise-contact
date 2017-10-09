<?php

namespace TddWizard\ExerciseContact\Controller\Form;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\ValidatorException;
use Magento\Customer\Model\Session as CustomerSession;
use TddWizard\ExerciseContact\Api\Data\InquiryInterfaceFactory;
use TddWizard\ExerciseContact\Api\InquiryRepositoryInterface;
use TddWizard\ExerciseContact\Model\Session;

class Save extends Action
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var InquiryRepositoryInterface
     */
    private $inquiryRepository;
    /**
     * @var InquiryInterfaceFactory
     */
    private $inquiryFactory;
    /**
     * @var CustomerSession
     */
    private $customerSession;

    public function __construct(
        Context $context,
        Session $session,
        CustomerSession $customerSession,
        InquiryRepositoryInterface $inquiryRepository,
        InquiryInterfaceFactory $inquiryFactory)
    {
        parent::__construct($context);
        $this->session = $session;
        $this->customerSession = $customerSession;
        $this->inquiryRepository = $inquiryRepository;
        $this->inquiryFactory = $inquiryFactory;
    }

    public function execute()
    {
        try {
            $this->validateRequest();
            $inquiry = $this->inquiryFactory->create();
            if ($this->customerSession->isLoggedIn()) {
                $inquiry->setEmail($this->customerSession->getCustomer()->getEmail());
            } else {
                $inquiry->setEmail($this->getRequest()->getParam('email'));
            }
            $inquiry->setMessage($this->getRequest()->getParam('message'));
            $this->inquiryRepository->save($inquiry);
            $this->messageManager->addSuccessMessage('We received your inquiry and will contact you shortly.');
        } catch (ValidatorException $e) {
            $this->session->saveFormData((array)$this->getRequest()->getParams());
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        return $this->redirectToForm();
    }

    private function validateRequest()
    {
        if (! $this->customerSession->isLoggedIn() && strpos($this->getRequest()->getParam('email'), '@') === false) {
            throw new ValidatorException(__('Please enter a valid email address.'));
        }
        if (trim($this->getRequest()->getParam('message')) === '') {
            throw new ValidatorException(__('Please enter a message.'));
        }
    }

    private function redirectToForm(): Redirect
    {
        /** @var Redirect $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $result->setPath('exercise_contact/form');
        return $result;
    }
}
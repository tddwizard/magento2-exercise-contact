<?php

namespace TddWizard\ExerciseContact\Service;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Message;
use TddWizard\ExerciseContact\Api\Data\InquiryInterface;
use TddWizard\ExerciseContact\Api\Data\InquiryInterfaceFactory;
use TddWizard\ExerciseContact\Api\InquiryRepositoryInterface;
use TddWizard\ExerciseContact\Model\Session;

class CreateInquiry
{
    /**
     * @var InquiryRepositoryInterface
     */
    private $repository;
    /**
     * @var InquiryInterfaceFactory
     */
    private $factory;
    /**
     * @var CustomerSession
     */
    private $customerSession;
    /**
     * @var Message\ManagerInterface
     */
    private $messageManager;
    /**
     * @var Session
     */
    private $contactSession;

    public function __construct(
        InquiryRepositoryInterface $repository,
        InquiryInterfaceFactory $factory,
        CustomerSession $customerSession,
        Session $contactSession,
        Message\ManagerInterface $messageManager)
    {
        $this->repository = $repository;
        $this->factory = $factory;
        $this->customerSession = $customerSession;
        $this->contactSession = $contactSession;
        $this->messageManager = $messageManager;
    }

    /**
     * @param string $message Message from user input
     * @param string|null $email Email address from user input
     * @return int Number of created inquiries
     */
    public function createFromInput(string $message, string $email = null) : int
    {
        if ($this->customerSession->isLoggedIn()) {
            $email = $this->customerSession->getCustomer()->getEmail();
        }
        if (! $this->validate($message, $email)) {
            $this->contactSession->saveFormData(['email' => $email, 'message' => $message]);
            return 0;
        }
        $this->repository->save($this->createInquiry($message, $email));
        $this->messageManager->addSuccessMessage(__('We received your inquiry and will contact you shortly.'));
        return 1;
    }

    private function validate(string $message, string $email = null)
    {
        if (trim($message) === '') {
            $this->messageManager->addErrorMessage(__('Please enter a message.'));
            return false;
        }
        if (false === strpos($email, '@')) {
            $this->messageManager->addErrorMessage(__('Please enter a valid email address.'));
            return false;
        }
        return true;
    }

    private function createInquiry(string $message, string $email) : InquiryInterface
    {
        $inquiry = $this->factory->create();
        $inquiry->setEmail($email);
        $inquiry->setMessage($message);
        return $inquiry;
    }
}
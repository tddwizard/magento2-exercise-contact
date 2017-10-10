<?php

namespace TddWizard\ExerciseContact\Service;

use Magento\Customer\Model\Session;
use TddWizard\ExerciseContact\Api\Data\InquiryInterfaceFactory;
use TddWizard\ExerciseContact\Api\InquiryRepositoryInterface;
use TddWizard\ExerciseContact\Test\Unit\InquiryDummy;

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
     * @var Session
     */
    private $customerSession;

    public function __construct(
        InquiryRepositoryInterface $repository,
        InquiryInterfaceFactory $factory,
        Session $customerSession)
    {
        $this->repository = $repository;
        $this->factory = $factory;
        $this->customerSession = $customerSession;
    }

    public function createFromInput(string $message, string $email = null)
    {
        if ($this->customerSession->isLoggedIn()) {
            $email = $this->customerSession->getCustomer()->getEmail();
        }
        if ($this->validate($message, $email)) {
            $inquiry = $this->factory->create();
            $inquiry->setEmail($email);
            $inquiry->setMessage($message);
            $this->repository->save($inquiry);
        }
    }

    private function validate(string $message, string $email = null)
    {
        if (trim($message) === '') {
            return false;
        }
        if (false === strpos($email, '@')) {
            return false;
        }
        return true;
    }
}
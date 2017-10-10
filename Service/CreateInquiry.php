<?php

namespace TddWizard\ExerciseContact\Service;

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

    public function __construct(InquiryRepositoryInterface $repository, InquiryInterfaceFactory $factory)
    {
        $this->repository = $repository;
        $this->factory = $factory;
    }

    public function createFromInput(string $message, string $email = null)
    {
        $inquiry = $this->factory->create();
        $inquiry->setEmail($email);
        $inquiry->setMessage($message);
        $this->repository->save($inquiry);
    }
}
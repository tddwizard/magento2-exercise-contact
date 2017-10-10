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
use TddWizard\ExerciseContact\Service\CreateInquiry;

class Save extends Action
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var CreateInquiry
     */
    private $createInquiry;

    public function __construct(
        Context $context,
        Session $session,
        CreateInquiry $createInquiry)
    {
        parent::__construct($context);
        $this->session = $session;
        $this->createInquiry = $createInquiry;
    }

    public function execute()
    {
        $createdInquiries = $this->createInquiry->createFromInput(
            $this->getRequest()->getParam('message'),
            $this->getRequest()->getParam('email')
        );
        if ($createdInquiries === 0) {
            $this->session->saveFormData((array)$this->getRequest()->getParams());
        }
        return $this->redirectToForm();
    }

    private function redirectToForm(): Redirect
    {
        /** @var Redirect $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $result->setPath('exercise_contact/form');
        return $result;
    }
}
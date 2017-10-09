<?php

namespace TddWizard\ExerciseContact\Controller\Form;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\ValidatorException;
use TddWizard\ExerciseContact\Model\Session;

class Save extends Action
{
    /**
     * @var Session
     */
    private $session;

    public function __construct(Context $context, Session $session)
    {
        parent::__construct($context);
        $this->session = $session;
    }

    public function execute()
    {
        try {
            $this->validateRequest();
            $this->messageManager->addSuccessMessage('We received your inquiry and will contact you shortly.');
        } catch (ValidatorException $e) {
            $this->session->saveFormData((array) $this->getRequest()->getParams());
            $this->messageManager->addErrorMessage($e->getMessage());
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

    private function validateRequest()
    {
        if (strpos($this->getRequest()->getParam('email'), '@') === false) {
            throw new ValidatorException(__('Please enter a valid email address.'));
        }
        if (trim($this->getRequest()->getParam('message')) === '') {
            throw new ValidatorException(__('Please enter a message.'));
        }
    }
}
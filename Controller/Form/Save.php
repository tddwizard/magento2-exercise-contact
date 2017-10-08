<?php

namespace TddWizard\ExerciseContact\Controller\Form;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\ValidatorException;

class Save extends Action
{
    public function execute()
    {
        try {
            $this->validateRequest();
            $this->messageManager->addSuccessMessage('We received your inquiry and will contact you shortly.');
        } catch (ValidatorException $e) {
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
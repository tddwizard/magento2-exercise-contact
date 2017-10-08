<?php

namespace TddWizard\ExerciseContact\Controller\Form;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;

class Save extends Action
{
    public function execute()
    {
        /** @var Redirect $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $result->setPath('exercise_contact/form');
        return $result;
    }
}
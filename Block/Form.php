<?php

namespace TddWizard\ExerciseContact\Block;

use Magento\Framework\View\Element\Template;
use TddWizard\ExerciseContact\Model\Session;

class Form extends Template
{
    /**
     * @var Session
     */
    private $session;

    public function __construct(Template\Context $context, Session $session, array $data = [])
    {
        parent::__construct($context, $data);
        $this->session = $session;
    }

    public function getSavedFormData() : array
    {
        return $this->session->getSavedFormData();
    }
}
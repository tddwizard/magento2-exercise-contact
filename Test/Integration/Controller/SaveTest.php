<?php

namespace TddWizard\ExerciseContact\Test\Integration\Controller\Frontend;

use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractController;
use TddWizard\ExerciseContact\Model\Session;
use TddWizard\ExerciseContact\Model\ResourceModel\Inquiry;

/**
 * @magentoDataFixtureBeforeTransaction truncateInquiries
 * @magentoDbIsolation enabled
 */
class SaveTest extends AbstractController
{

    public static function truncateInquiries()
    {
        /** @var Inquiry $inquiry */
        $inquiry = Bootstrap::getObjectManager()->create(Inquiry::class);
        $inquiry->getConnection()->truncateTable($inquiry->getMainTable());
    }

    public static function dataInvalidInput()
    {
        return [
            'empty email'   => [
                '',
                'Hello, World!',
                'Please enter a valid email address.',
            ],
            'invalid email' => [
                'test',
                'Hello, World!',
                'Please enter a valid email address.',
            ],
            'empty message' => [
                'test@example.com',
                ' ',
                'Please enter a message.',
            ],
        ];
    }

    /**
     * @magentoAppArea frontend
     */
    public function testRedirectWithSuccessMessage()
    {
        $this->getRequest()->setMethod('POST');
        $this->getRequest()->setPostValue('email', 'test@example.com');
        $this->getRequest()->setPostValue('message', 'Hello, World!');
        $this->dispatch('exercise_contact/form/save');
        $this->assertRedirect($this->stringContains('exercise_contact/form'));
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages(
            $this->equalTo(['We received your inquiry and will contact you shortly.']),
            MessageInterface::TYPE_SUCCESS
        );
    }

    /**
     * @dataProvider dataInvalidInput
     * @magentoAppArea frontend
     */
    public function testRedirectWithErrorMessageOnInvalidInput($email, $message, $expectedErrorMessage)
    {
        $this->getRequest()->setMethod('POST');
        $this->getRequest()->setPostValue('email', $email);
        $this->getRequest()->setPostValue('message', $message);
        $this->dispatch('exercise_contact/form/save');
        $this->assertRedirect($this->stringContains('exercise_contact/form'));
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_SUCCESS);
        $this->assertSessionMessages(
            $this->equalTo([$expectedErrorMessage]),
            MessageInterface::TYPE_ERROR
        );
        /** @var Session $session */
        $session = $this->_objectManager->get(Session::class);
        $this->assertArraySubset(
            ['email' => $email, 'message' => $message],
            $session->getSavedFormData(),
            'Form data should be saved in session after error'
        );
    }
}
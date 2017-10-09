<?php

namespace TddWizard\ExerciseContact\Test\Integration\Controller\Frontend;

use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\TestCase\AbstractController;
use TddWizard\ExerciseContact\Api\InquiryRepositoryInterface;
use TddWizard\ExerciseContact\Model\ResourceModel\Inquiry as InquiryResource;
use TddWizard\ExerciseContact\Model\Session;

/**
 * @magentoDbIsolation enabled
 */
class SaveTest extends AbstractController
{
    protected function setUp()
    {
        parent::setUp();
        /** @var InquiryResource $inquiryResource */
        $inquiryResource = $this->_objectManager->create(InquiryResource::class);
        $inquiryResource->getConnection()->truncateTable($inquiryResource->getMainTable());
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
    public function testRedirectWithSuccessMessageAndInquiryIsSaved()
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

        /** @var InquiryRepositoryInterface $repository */
        $repository = $this->_objectManager->get(InquiryRepositoryInterface::class);
        $this->assertEquals(1, $repository->getList(new SearchCriteria())->getTotalCount());
    }

    /**
     * @magentoAppArea frontend
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testInquiryIsSavedWithCustomerEmailForLoggedInCustomer()
    {
        /** @var \Magento\Customer\Model\Session $customerSession */
        $customerSession = $this->_objectManager->get(\Magento\Customer\Model\Session::class);
        $customerSession->loginById(1);
        $this->getRequest()->setMethod('POST');
        $this->getRequest()->setPostValue('message', 'Hello, World!');
        $this->dispatch('exercise_contact/form/save');
        $this->assertRedirect($this->stringContains('exercise_contact/form'));
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages(
            $this->equalTo(['We received your inquiry and will contact you shortly.']),
            MessageInterface::TYPE_SUCCESS
        );

        /** @var InquiryRepositoryInterface $repository */
        $repository = $this->_objectManager->get(InquiryRepositoryInterface::class);
        $allInquiries = $repository->getList(new SearchCriteria());
        $this->assertEquals(1, $allInquiries->getTotalCount());
        $this->assertEquals('customer@example.com', array_values($allInquiries->getItems())[0]->getEmail());
    }

    /**
     * @dataProvider dataInvalidInput
     * @magentoAppArea frontend
     */
    public function testRedirectWithErrorMessageAndInquiryIsNotSavedOnInvalidInput($email, $message,
        $expectedErrorMessage)
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
        $this->assertEquals(
            ['email' => $email, 'message' => $message],
            $session->getSavedFormData(),
            'Form data should be saved in session after error'
        );

        /** @var InquiryRepositoryInterface $repository */
        $repository = $this->_objectManager->get(InquiryRepositoryInterface::class);
        $this->assertEquals(0, $repository->getList(new SearchCriteria())->getTotalCount());
    }
}
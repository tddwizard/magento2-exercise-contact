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
            'empty message' => ['test@example.com', ' '],
        ];
    }

    /**
     * @magentoAppArea frontend
     */
    public function testRedirectWithSuccessMessageAndInquiryIsSaved()
    {
        $this->dispatchSaveAction('test@example.com', 'Hello, World!');
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages($this->logicalNot($this->isEmpty()), MessageInterface::TYPE_SUCCESS);

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
        $this->dispatchSaveAction(null, 'Hello, World!');
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages($this->logicalNot($this->isEmpty()), MessageInterface::TYPE_SUCCESS);

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
    public function testRedirectWithErrorMessageAndInquiryIsNotSavedOnInvalidInput($email, $message)
    {
        $this->dispatchSaveAction($email, $message);
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_SUCCESS);
        $this->assertSessionMessages($this->logicalNot($this->isEmpty()), MessageInterface::TYPE_ERROR);
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

    /**
     * @param $email
     * @param $message
     */
    private function dispatchSaveAction($email, $message)
    {
        $this->getRequest()->setMethod('POST');
        $this->getRequest()->setPostValue('email', $email);
        $this->getRequest()->setPostValue('message', $message);
        $this->dispatch('exercise_contact/form/save');
        $this->assertRedirect($this->stringContains('exercise_contact/form'));
    }
}
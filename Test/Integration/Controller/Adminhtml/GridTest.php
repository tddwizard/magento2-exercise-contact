<?php

namespace TddWizard\ExerciseContact\Test\Integration\Controller\Adminhtml;

use Magento\TestFramework\TestCase\AbstractBackendController;
use TddWizard\ExerciseContact\Api\Data\InquiryInterface;
use TddWizard\ExerciseContact\Api\InquiryRepositoryInterface;

/**
 * @magentoDbIsolation enabled
 */
class GridTest extends AbstractBackendController
{

    protected function setUp()
    {
        parent::setUp();
        /** @var InquiryRepositoryInterface $repository */
        $repository = $this->_objectManager->get(InquiryRepositoryInterface::class);
        /** @var InquiryInterface $inquiry */
        $inquiry = $this->_objectManager->create(InquiryInterface::class);
        $inquiry->setEmail('test@example.com');
        $inquiry->setMessage('I should be visible in the grid');
        $repository->save($inquiry);
    }

    public function testGridShowsSavedInquiry()
    {
        $this->dispatch('backend/tddwizard_exercisecontact/inquiry');
        $this->assertContains('I should be visible in the grid', $this->getResponse()->getBody());
    }
}
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
    /**
     * The resource used to authorize action
     *
     * @var string
     */
    protected $resource = 'TddWizard_ExerciseContact::Inquiry';

    /**
     * The uri at which to access the controller
     *
     * @var string
     */
    protected $uri = 'backend/tddwizard_exercisecontact/inquiry';

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
        $this->dispatch($this->uri);
        $this->assertContains('I should be visible in the grid', $this->getResponse()->getBody());
    }
}
<?php

namespace TddWizard\ExerciseContact\Test\Integration;

use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;
use TddWizard\ExerciseContact\Api\Data\InquiryInterface;
use TddWizard\ExerciseContact\Api\Data\InquirySearchResultsInterface;
use TddWizard\ExerciseContact\Api\InquiryRepositoryInterface;
use TddWizard\ExerciseContact\Model\Inquiry;

/**
 * @magentoDbIsolation enabled
 */
class InquiryRepositoryTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var InquiryRepositoryInterface
     */
    private $repository;

    /**
     * @var Inquiry
     */
    private $inquiry;

    protected function setUp(): void
    {
        $this->objectManager = ObjectManager::getInstance();
        /** @var InquiryRepositoryInterface $repository */
        $this->repository = $this->objectManager->get(InquiryRepositoryInterface::class);
        $this->setUpInquiryFixture();

    }

    private function setUpInquiryFixture()
    {
        /** @var Inquiry $inquiry */
        $this->inquiry = $this->objectManager->create(InquiryInterface::class);
        $this->inquiry->setEmail('test@example.com');
        $this->inquiry->setMessage('HALLO');
        $this->repository->save($this->inquiry);
    }

    protected function tearDown(): void
    {
        $this->repository->deleteById($this->inquiry->getId());
    }

    public function testGetById()
    {
        $loadedInquiry = $this->repository->getById($this->inquiry->getId());
        $this->assertEquals('HALLO', $loadedInquiry->getMessage());
    }

    public function testGetList()
    {
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->objectManager->create(SearchCriteriaBuilder::class);
        $searchCriteriaBuilder->addFilter('email', 'test@example.com');
        $list = $this->repository->getList($searchCriteriaBuilder->create());
        $this->assertInstanceOf(InquirySearchResultsInterface::class, $list);
        $this->assertCount(1, $list->getItems());
        foreach ($list->getItems() as $item) {
            $this->assertEquals('test@example.com', $item->getEmail());
        }
    }
}

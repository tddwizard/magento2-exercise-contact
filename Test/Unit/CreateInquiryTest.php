<?php

namespace TddWizard\ExerciseContact\Test\Unit;

use PHPUnit\Framework\TestCase;
use TddWizard\ExerciseContact\Api\Data\InquiryInterfaceFactory;
use TddWizard\ExerciseContact\Model\Inquiry;
use TddWizard\ExerciseContact\Service\CreateInquiry;

class CreateInquiryTest extends TestCase
{
    /**
     * @var Fake\InquiryMemoryRepository
     */
    private $repository;
    /**
     * @var InquiryInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject $factory
     */
    private $factoryStub;

    protected function setUp()
    {
        $this->repository = new Fake\InquiryMemoryRepository();
        $this->factoryStub = $this->createMock(InquiryInterfaceFactory::class);
    }

    public function testNewInquiryIsPopulatedAndSaved()
    {
        /** @var Inquiry|\PHPUnit_Framework_MockObject_MockObject $inquiryFromFactory */
        $inquiryFromFactory = $this->createPartialMock(Inquiry::class, []);
        $this->factoryStub->method('create')->willReturn($inquiryFromFactory);

        $createInquiryService = new CreateInquiry($this->repository, $this->factoryStub);
        $createInquiryService->createFromInput('Hallo', 'ich@example.com');

        $this->assertEquals('ich@example.com', $inquiryFromFactory->getEmail());
        $this->assertEquals('Hallo', $inquiryFromFactory->getMessage());
        $this->assertEquals([$inquiryFromFactory], $this->repository->inquiries, 'Inquiry should be saved in repository');
    }
}

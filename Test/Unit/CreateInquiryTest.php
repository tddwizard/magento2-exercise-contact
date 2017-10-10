<?php

namespace TddWizard\ExerciseContact\Test\Unit;

use PHPUnit\Framework\TestCase;
use TddWizard\ExerciseContact\Api\Data\InquiryInterfaceFactory;
use TddWizard\ExerciseContact\Model\Inquiry;
use TddWizard\ExerciseContact\Service\CreateInquiry;

class CreateInquiryTest extends TestCase
{
    /**
     * @var CreateInquiry
     */
    private $createInquiryService;
    /**
     * @var Fake\InquiryMemoryRepository
     */
    private $repository;
    /**
     * @var InquiryInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject $factory
     */
    private $factoryStub;
    /**
     * @var Inquiry|\PHPUnit_Framework_MockObject_MockObject $inquiryFromFactory
     */
    private $inquiryStub;

    protected function setUp()
    {
        $this->repository = new Fake\InquiryMemoryRepository();
        $this->factoryStub = $this->createMock(InquiryInterfaceFactory::class);
        $this->inquiryStub = $this->createPartialMock(Inquiry::class, []);
        $this->factoryStub->method('create')->willReturn($this->inquiryStub);
        $this->createInquiryService = new CreateInquiry($this->repository, $this->factoryStub);
    }

    public function testNewInquiryIsPopulatedAndSaved()
    {
        $this->createInquiryService->createFromInput('Hallo', 'ich@example.com');

        $this->assertEquals('ich@example.com', $this->inquiryStub->getEmail());
        $this->assertEquals('Hallo', $this->inquiryStub->getMessage());
        $this->assertEquals([$this->inquiryStub], $this->repository->inquiries, 'Inquiry should be saved in repository');
    }

    public static function dataInvalidInput()
    {
        return [
            'no message' => ['', 'test@example.com'],
            'empty message' => ['   ', 'test@example.com'],
            'empty email' => ['Hello', ''],
            'invalid email' => ['Hello', 'not an email'],
        ];
    }

    /**
     * @dataProvider dataInvalidInput
     */
    public function testInquiryIsNotSavedOnInvalidInput(string $message, string $email)
    {
        $this->createInquiryService->createFromInput($message, $email);
        $this->assertEmpty($this->repository->inquiries, 'No inquiry should be saved in repository');
    }
}

<?php

namespace TddWizard\ExerciseContact\Test\Unit;

use Magento\Customer\Model\Data\Customer;
use Magento\Customer\Model\Session;
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
    /**
     * @var Session|\PHPUnit_Framework_MockObject_MockObject
     */
    private $sessionStub;

    protected function setUp()
    {
        $this->repository = new Fake\InquiryMemoryRepository();
        $this->inquiryStub = $this->createPartialMock(Inquiry::class, []);
        $this->factoryStub = $this->createMock(InquiryInterfaceFactory::class);
        $this->factoryStub->method('create')->willReturn($this->inquiryStub);
        $this->sessionStub = $this->createMock(Session::class);
        $this->createInquiryService = new CreateInquiry($this->repository, $this->factoryStub, $this->sessionStub);
    }

    public function testNewInquiryIsPopulatedAndSaved()
    {
        $this->createInquiryService->createFromInput('Hallo', 'ich@example.com');
        $this->assertInquirySavedWithData('ich@example.com', 'Hallo');
    }

    public function testNewInquiryIsSavedWithCustomerEmailAddress()
    {
        $customerStub = $this->createMock(Customer::class);
        $customerStub->method('getEmail')->willReturn('customer@example.com');
        $this->sessionStub->method('isLoggedIn')->willReturn(true);
        $this->sessionStub->method('getCustomer')->willReturn($customerStub);
        $this->createInquiryService->createFromInput('Hallo');
        $this->assertInquirySavedWithData('customer@example.com', 'Hallo');
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

    private function assertInquirySavedWithData($expectedEmail, $expectedMessage)
    {
        $this->assertEquals($expectedEmail, $this->inquiryStub->getEmail());
        $this->assertEquals($expectedMessage, $this->inquiryStub->getMessage());
        $this->assertEquals(
            [$this->inquiryStub],
            $this->repository->inquiries,
            'Inquiry should be saved in repository'
        );
    }
}

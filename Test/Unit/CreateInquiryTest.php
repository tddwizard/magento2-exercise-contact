<?php

namespace TddWizard\ExerciseContact\Test\Unit;

use Magento\Customer\Model\Data\Customer;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Message;
use PHPUnit\Framework\TestCase;
use TddWizard\ExerciseContact\Api\Data\InquiryInterfaceFactory;
use TddWizard\ExerciseContact\Model\Inquiry;
use TddWizard\ExerciseContact\Model\Session;
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
     * @var CustomerSession|\PHPUnit_Framework_MockObject_MockObject
     */
    private $customerSessionStub;
    /**
     * @var Session|\PHPUnit_Framework_MockObject_MockObject
     */
    private $contactSessionMock;
    /**
     * @var Message\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $messageManagerMock;

    protected function setUp()
    {
        $this->repository = new Fake\InquiryMemoryRepository();
        $this->inquiryStub = $this->createPartialMock(Inquiry::class, []);
        $this->factoryStub = $this->createMock(InquiryInterfaceFactory::class);
        $this->factoryStub->method('create')->willReturn($this->inquiryStub);
        $this->customerSessionStub = $this->createMock(CustomerSession::class);
        $this->contactSessionMock = $this->createMock(Session::class);
        $this->messageManagerMock = $this->createMock(Message\ManagerInterface::class);
        $this->createInquiryService = new CreateInquiry(
            $this->repository,
            $this->factoryStub,
            $this->customerSessionStub,
            $this->contactSessionMock,
            $this->messageManagerMock
        );
    }

    public function testNewInquiryIsPopulatedAndSaved()
    {
        $this->messageManagerMock->expects($this->once())->method('addSuccessMessage')->with(
            'We received your inquiry and will contact you shortly.'
        );
        $this->createInquiryService->createFromInput('Hallo', 'ich@example.com');
        $this->assertInquirySavedWithData('ich@example.com', 'Hallo');
    }

    public function testNewInquiryIsSavedWithCustomerEmailAddress()
    {
        $this->messageManagerMock->expects($this->once())->method('addSuccessMessage')->with(
            'We received your inquiry and will contact you shortly.'
        );
        $customerStub = $this->createMock(Customer::class);
        $customerStub->method('getEmail')->willReturn('customer@example.com');
        $this->customerSessionStub->method('isLoggedIn')->willReturn(true);
        $this->customerSessionStub->method('getCustomer')->willReturn($customerStub);
        $this->createInquiryService->createFromInput('Hallo');
        $this->assertInquirySavedWithData('customer@example.com', 'Hallo');
    }

    public static function dataInvalidInput()
    {
        return [
            'no message'    => ['', 'test@example.com', 'Please enter a message.'],
            'empty message' => ['   ', 'test@example.com', 'Please enter a message.'],
            'empty email'   => ['Hello', '', 'Please enter a valid email address.'],
            'invalid email' => ['Hello', 'not an email', 'Please enter a valid email address.'],
        ];
    }

    /**
     * @dataProvider dataInvalidInput
     */
    public function testInquiryIsNotSavedOnInvalidInput(string $message, string $email, string $expectedMessage)
    {
        $this->messageManagerMock->expects($this->once())->method('addErrorMessage')->with($expectedMessage);
        $this->contactSessionMock->expects($this->once())->method('saveFormData')->with(
            [
                'email'   => $email,
                'message' => $message,
            ]
        );
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

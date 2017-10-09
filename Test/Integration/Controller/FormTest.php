<?php

namespace TddWizard\ExerciseContact\Test\Integration\Controller\Frontend;

use Magento\TestFramework\TestCase\AbstractController;
use TddWizard\ExerciseContact\Model\Session;

class FormTest extends AbstractController
{
    const XPATH_FORM = '//form[@id="tddwizard_contact"]';

    /**
     * @magentoAppArea frontend
     */
    public function testFormIsDisplayed()
    {
        $this->dispatchFormAction();
        $this->assertFormRendered();
        $this->assertDomElementPresent(
            self::XPATH_FORM . '//input[@name="email"]',
            "Form should contain email input"
        );
        $this->assertDomElementPresent(
            self::XPATH_FORM . '//textarea[@name="message"]',
            "Form should contain message input"
        );
    }

    /**
     * @magentoAppArea frontend
     */
    public function testFormIsFilledWithSavedSessionData()
    {
        /** @var Session $session */
        $session = $this->_objectManager->get(Session::class);
        $session->saveFormData(['email' => 'saved@example.com', 'message' => 'Saved Message']);
        $this->dispatchFormAction();
        $this->assertDomElementContains(
            self::XPATH_FORM . '//input[@name="email"]',
            'value="saved@example.com"'
        );
        $this->assertDomElementContains(
            self::XPATH_FORM . '//textarea[@name="message"]',
            'Saved Message'
        );
        $this->assertEmpty($session->getSavedFormData(), 'Saved form data should be removed from session');
    }

    /**
     * @magentoAppArea frontend
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testFormDoesNotContainEmailInputForLoggedInCustomer()
    {
        /** @var \Magento\Customer\Model\Session $customerSession */
        $customerSession = $this->_objectManager->get(\Magento\Customer\Model\Session::class);
        $customerSession->loginById(1);
        $this->dispatchFormAction();
        $this->assertFormRendered();
        $this->assertDomElementPresent(
            self::XPATH_FORM . '//textarea[@name="message"]',
            "Form should contain message input"
        );
        $this->assertDomElementNotPresent(
            self::XPATH_FORM . '//input[@name="email"]',
            "Form should not contain email input"
        );
    }


    private function dispatchFormAction()
    {
        $this->dispatch('exercise_contact/form');
        $this->assertEquals(
            'form',
            $this->getRequest()->getControllerName(),
            'Form controller should be dispatched successfully'
        );
    }

    private function assertFormRendered()
    {
        $this->assertDomElementPresent(
            self::XPATH_FORM,
            "Form element should be found"
        );
        $this->assertDomElementPresent(
            self::XPATH_FORM . '[contains(@action,"exercise_contact/form/save")]',
            "Form action should be save action"
        );
        $this->assertDomElementPresent(
            self::XPATH_FORM . '//button[@type="submit"]',
            "Form should contain submit button"
        );
    }

    private function assertDomElementPresent(string $xpath, string $message = '')
    {
        $this->assertDomElementCount($xpath, 1, $message);
    }

    private function assertDomElementNotPresent(string $xpath, string $message = '')
    {
        $this->assertDomElementCount($xpath, 0, $message);
    }

    private function assertDomElementCount(string $xpath, int $expectedCount, string $message = '')
    {
        $dom = $this->getResponseDom();
        $this->assertEquals($expectedCount, (new \DOMXPath($dom))->query($xpath)->length, $message);
    }

    private function assertDomElementContains(string $xpath, string $expectedString, string $message = '')
    {
        $dom = $this->getResponseDom();
        $this->assertContains($expectedString, $dom->saveHTML((new \DOMXPath($dom))->query($xpath)->item(0)), $message);
    }
    private function getResponseDom(): \DOMDocument
    {
        $dom = new \DOMDocument();
        \libxml_use_internal_errors(true);
        $dom->loadHTML($this->getResponse()->getBody());
        \libxml_use_internal_errors(false);
        return $dom;
    }

}
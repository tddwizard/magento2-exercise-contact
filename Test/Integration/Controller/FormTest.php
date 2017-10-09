<?php

namespace TddWizard\ExerciseContact\Test\Integration\Controller\Frontend;

use Magento\TestFramework\TestCase\AbstractController;
use TddWizard\ExerciseContact\Model\Session;

class FormTest extends AbstractController
{
    /**
     * @magentoAppArea frontend
     */
    public function testFormIsDisplayed()
    {
        $this->dispatch('exercise_contact/form');
        $this->assertEquals(
            'form',
            $this->getRequest()->getControllerName(),
            'Form controller should be dispatched successfully'
        );
        $this->assertDomElementPresent(
            '//form[@id="tddwizard_contact"]',
            "Form element should be found"
        );
        $this->assertDomElementPresent(
            '//form[@id="tddwizard_contact"][contains(@action,"exercise_contact/form/save")]',
            "Form action should be save action"
        );
        $this->assertDomElementPresent(
            '//form[@id="tddwizard_contact"]//input[@name="email"]',
            "Form should contain email input"
        );
        $this->assertDomElementPresent(
            '//form[@id="tddwizard_contact"]//textarea[@name="message"]',
            "Form should contain message input"
        );
        $this->assertDomElementPresent(
            '//form[@id="tddwizard_contact"]//button[@type="submit"]',
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

    private function getResponseDom(): \DOMDocument
    {
        $dom = new \DOMDocument();
        \libxml_use_internal_errors(true);
        $dom->loadHTML($this->getResponse()->getBody());
        \libxml_use_internal_errors(false);
        return $dom;
    }

    /**
     * @magentoAppArea frontend
     */
    public function testFormIsFilledWithSavedSessionData()
    {
        /** @var Session $session */
        $session = $this->_objectManager->get(Session::class);
        $session->saveFormData(['email' => 'saved@example.com', 'message' => 'Saved Message']);
        $this->dispatch('exercise_contact/form');
        $this->assertDomElementContains(
            '//form[@id="tddwizard_contact"]//input[@name="email"]',
            'value="saved@example.com"'
        );
        $this->assertDomElementContains(
            '//form[@id="tddwizard_contact"]//textarea[@name="message"]',
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
        $this->dispatch('exercise_contact/form');
        $this->assertDomElementPresent(
            '//form[@id="tddwizard_contact"][contains(@action,"exercise_contact/form/save")]',
            "Form with save action should be present"
        );
        $this->assertDomElementPresent(
            '//form[@id="tddwizard_contact"]//textarea[@name="message"]',
            "Form should contain message input"
        );
        $this->assertDomElementPresent(
            '//form[@id="tddwizard_contact"]//button[@type="submit"]',
            "Form should contain submit button"
        );
        $this->assertDomElementNotPresent(
            '//form[@id="tddwizard_contact"]//input[@name="email"]',
            "Form should not contain email input"
        );
    }

    private function assertDomElementContains(string $xpath, string $expectedString, string $message = '')
    {
        $dom = $this->getResponseDom();
        $this->assertContains($expectedString, $dom->saveHTML((new \DOMXPath($dom))->query($xpath)->item(0)), $message);
    }
}
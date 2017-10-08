<?php

namespace TddWizard\ExerciseContact\Test\Integration\Controller\Frontend;

use Magento\TestFramework\TestCase\AbstractController;

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
            '//form[@id="tddwizard_contact"]//input[@name="email"]',
            "Form should contain email input"
        );
        $this->assertDomElementPresent(
            '//form[@id="tddwizard_contact"]//textarea[@name="message"]',
            "Form should contain message input"
        );
    }

    private function assertDomElementPresent(string $xpath, string $message = '')
    {
        $this->assertDomElementCount($xpath, 1, $message);
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
}
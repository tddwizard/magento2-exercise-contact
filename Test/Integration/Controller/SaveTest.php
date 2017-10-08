<?php

namespace TddWizard\ExerciseContact\Test\Integration\Controller\Frontend;

use Magento\TestFramework\TestCase\AbstractController;

class SaveTest extends AbstractController
{
    /**
     * @magentoAppArea frontend
     */
    public function testRedirect()
    {
        $this->dispatch('exercise_contact/form/save');
        $this->assertRedirect($this->stringContains('exercise_contact/form'));
    }
}
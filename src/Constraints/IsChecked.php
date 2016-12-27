<?php

namespace SeleniumTesting\Constraints;

class IsChecked extends FormFieldConstraint
{

    /**
     * Create a new constraint instance.
     *
     * @param  string $selector
     */
    public function __construct($selector)
    {
        $this->selector = $selector;
    }

    /**
     * Get the valid elements.
     *
     * @return string
     */
    protected function validElements()
    {
        return "input[type='checkbox']";
    }

    /**
     * Determine if the checkbox is checked.
     *
     * @param  Crawler|string $crawler
     *
     * @return bool
     */
    public function matches($crawler)
    {
        $crawler = $this->crawler($crawler);

        return $this->field($crawler)->selected();
    }

    /**
     * Return the description of the failure.
     *
     * @return string
     */
    protected function getFailureDescription()
    {
        return "the checkbox [{$this->selector}] is checked";
    }

    /**
     * Returns the reversed description of the failure.
     *
     * @return string
     */
    protected function getReverseFailureDescription()
    {
        return "the checkbox [{$this->selector}] is not checked";
    }
}

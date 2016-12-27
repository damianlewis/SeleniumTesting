<?php

namespace SeleniumTesting\Constraints;

class IsDisabled extends FormFieldConstraint
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
        return "*";
    }

    /**
     * Determine if the element is disabled.
     *
     * @param  Crawler|string $crawler
     *
     * @return bool
     */
    public function matches($crawler)
    {
        $crawler = $this->crawler($crawler);

        return ! $this->field($crawler)->element()->enabled();
    }

    /**
     * Return the description of the failure.
     *
     * @return string
     */
    protected function getFailureDescription()
    {
        return "the element [{$this->selector}] is disabled";
    }

    /**
     * Returns the reversed description of the failure.
     *
     * @return string
     */
    protected function getReverseFailureDescription()
    {
        return "the element [{$this->selector}] is not disabled";
    }
}

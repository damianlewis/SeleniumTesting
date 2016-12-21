<?php

namespace SeleniumTesting\Constraints;

class HasSource extends PageConstraint
{

    /**
     * The expected HTML source.
     *
     * @var string
     */
    protected $source;

    /**
     * Create a new constraint instance.
     *
     * @param string $source
     */
    public function __construct(string $source)
    {
        $this->source = $source;
    }

    /**
     * Check if the element is found in the given crawler.
     *
     * @param \SeleniumTesting\Crawler $crawler
     *
     * @return bool
     */
    public function matches($crawler)
    {
        $elements = $this->crawler($crawler)->filter($this->getSelector());

        if ($elements->count() == 0) {
            return false;
        }

        return true;
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return "the HTML [{$this->source}]";
    }

    /**
     * Get the selector that's relevant to the constraint.
     *
     * @return string
     */
    private function getSelector()
    {
        return "//*[contains(text(), '{$this->source}')]";
    }
}
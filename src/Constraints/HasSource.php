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
    public function __construct($source)
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
    protected function matches($crawler)
    {
        $pattern = $this->getEscapedPattern($this->source);

        return preg_match("/{$pattern}/i", $this->html($crawler));
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
}
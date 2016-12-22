<?php

namespace SeleniumTesting\Constraints;

class HasText extends PageConstraint
{

    /**
     * The expected text.
     *
     * @var string
     */
    protected $text;

    /**
     * Create a new constraint instance.
     *
     * @param  string $text
     */
    public function __construct($text)
    {
        $this->text = $text;
    }

    /**
     * Check if the plain text is found in the given crawler.
     *
     * @param  \SeleniumTesting\Crawler $crawler
     *
     * @return bool
     */
    protected function matches($crawler)
    {
        $pattern = $this->getEscapedPattern($this->text);

        return preg_match("/{$pattern}/i", $this->text($crawler));
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return "the text [{$this->text}]";
    }
}

<?php

namespace SeleniumTesting\Constraints;

class HasButton extends PageConstraint
{

    /**
     * The text expected to be found.
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
     * Check if the button is found in the given crawler.
     *
     * @param  \SeleniumTesting\Crawler|string $crawler
     *
     * @return bool
     */
    protected function matches($crawler)
    {
        $buttons = $this->crawler($crawler)->selectButton($this->text);

        if ($buttons->count() == 0) {
            return false;
        }

        return true;
    }

    /**
     * Returns the description of the failure.
     *
     * @return string
     */
    protected function getFailureDescription()
    {
        $description = "the page has a button with the text [{$this->text}]";

        return $description;
    }

    /**
     * Returns the reversed description of the failure.
     *
     * @return string
     */
    protected function getReverseFailureDescription()
    {
        $description = "the page does not have a button with the text [{$this->text}]";

        return $description;
    }
}

<?php

namespace SeleniumTesting\Constraints;

class HasInElement extends PageConstraint
{

    /**
     * The css selector, id, name or class attribute or tag name of the element.
     *
     * @var string
     */
    protected $selector;

    /**
     * The text expected to be found.
     *
     * @var string
     */
    protected $text;

    /**
     * Create a new constraint instance.
     *
     * @param  string $selector
     * @param  string $text
     *
     */
    public function __construct($selector, $text)
    {
        $this->selector = $selector;
        $this->text = (string)$text;
    }

    /**
     * Check if the source or text is found within the element in the given crawler.
     *
     * @param  \SeleniumTesting\Crawler $crawler
     *
     * @return bool
     */
    protected function matches($crawler)
    {
        $elements = $this->crawler($crawler)->filter($this->selector);

        foreach ($elements as $element) {
            if (str_contains($element->text(), $this->text)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the description of the failure.
     *
     * @return string
     */
    protected function getFailureDescription()
    {
        return sprintf('[%s] contains %s', $this->selector, $this->text);
    }

    /**
     * Returns the reversed description of the failure.
     *
     * @return string
     */
    protected function getReverseFailureDescription()
    {
        return sprintf('[%s] does not contain %s', $this->selector, $this->text);
    }
}

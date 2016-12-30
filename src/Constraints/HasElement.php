<?php

namespace SeleniumTesting\Constraints;

use PHPUnit_Extensions_Selenium2TestCase_Element as Selenium2TestCase_Element;

class HasElement extends PageConstraint
{

    /**
     * The css selector, id, name or class attribute or tag name of the element.
     *
     * @var string
     */
    protected $selector;

    /**
     * The attributes the element should have.
     *
     * @var array
     */
    protected $attributes;

    /**
     * The number of times the element is expected to be found.
     *
     * @var int
     */
    protected $count;

    /**
     * The number of times the element was actually found.
     *
     * @var int
     */
    protected $actualCount;

    /**
     * Create a new constraint instance.
     *
     * @param string $selector
     * @param array  $attributes
     * @param int    $count
     */
    public function __construct($selector, array $attributes = [], $count = null)
    {
        $this->selector = $selector;
        $this->attributes = $attributes;
        $this->count = $count;
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
        $elements = $this->crawler($crawler)->filter($this->selector);

        if ($elements->count() == 0) {
            return false;
        }

        if (empty($this->attributes) && is_null($this->count)) {
            return true;
        }

        if (! empty($this->attributes)) {
            $elements = $elements->reduce(function ($element) {
                return $this->hasAttributes($element);
            });
        }

        if (is_null($this->count)) {
            return $elements->count() > 0;
        }

        return ($this->actualCount = $elements->count()) === $this->count;
    }

    /**
     * Determines if the given element has the attributes.
     *
     * @param Selenium2TestCase_Element $element
     *
     * @return bool
     */
    private function hasAttributes(Selenium2TestCase_Element $element)
    {
        foreach ($this->attributes as $name => $value) {
            if ($element->attribute($name) != $value) {
                return false;
            }
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
        $message = "the element [{$this->selector}]";

        if (! empty($this->attributes)) {
            $message .= ' with the attributes '.json_encode($this->attributes);
        }

        if (! is_null($this->count)) {
            $message .= ' with an expected count of '.$this->count.' [actual count was '.$this->actualCount.']';
        }

        return $message;
    }
}
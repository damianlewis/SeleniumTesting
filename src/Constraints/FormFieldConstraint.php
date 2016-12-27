<?php

namespace SeleniumTesting\Constraints;

use SeleniumTesting\Crawler;

abstract class FormFieldConstraint extends PageConstraint
{

    /**
     * The name or ID of the element.
     *
     * @var string
     */
    protected $selector;

    /**
     * The expected value.
     *
     * @var string
     */
    protected $value;

    /**
     * Create a new constraint instance.
     *
     * @param  string $selector
     * @param  mixed  $value
     *
     */
    public function __construct($selector, $value)
    {
        $this->selector = $selector;
        $this->value = (string)$value;
    }

    /**
     * Get the valid elements.
     *
     * Multiple elements should be separated by commas without spaces.
     *
     * @return string
     */
    abstract protected function validElements();

    /**
     * Get the form field.
     *
     * @param  Crawler $crawler
     *
     * @return Crawler
     *
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    protected function field(Crawler $crawler)
    {
        $field = $crawler->filter(implode(', ', $this->getElements()));

        if ($field->count() > 0) {
            return $field;
        }

        $this->fail($crawler, sprintf(
            'There is no %s with the name or ID [%s]',
            $this->validElements(), $this->selector
        ));
    }

    /**
     * Get the elements relevant to the selector.
     *
     * @return array
     */
    protected function getElements()
    {
        $name = ltrim($this->selector, '#');

        return collect(explode(',', $this->validElements()))->map(function ($element) use ($name) {
            $selector = "{$element}#{$name}";

            preg_match('/\[.*\]/', $element, $options);

            if (! empty($options)) {
                $tag = preg_split('/\[.*\]/', $element)[0];
                $options = trim($options[0], '[,]');
                $selector .= ", //{$tag}[@{$options}][@name='{$name}']";
            } else {
                $selector .= ", //{$element}[@name='{$name}']";
            }

            return $selector;
        })->all();
    }
}

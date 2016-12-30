<?php

namespace SeleniumTesting\Constraints;

use SeleniumTesting\Crawler;

class IsSelected extends FormFieldConstraint
{

    /**
     * Get the valid elements.
     *
     * @return string
     */
    protected function validElements()
    {
        return "select,input[type='radio']";
    }

    /**
     * Determine if the select or radio element is selected.
     *
     * @param  Crawler|string $crawler
     *
     * @return bool
     */
    protected function matches($crawler)
    {
        $crawler = $this->crawler($crawler);

        return in_array($this->value, $this->getSelectedValue($crawler));
    }

    /**
     * Get the selected value of a select field or radio group.
     *
     * @param  Crawler $crawler
     *
     * @return array
     *
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    private function getSelectedValue(Crawler $crawler)
    {
        $field = $this->field($crawler);

        return $field->elementName() == 'select'
            ? $this->getSelectedValueFromSelect($field)
            : [$this->getCheckedValueFromRadioGroup($field)];
    }

    /**
     * Get the selected value from a select field.
     *
     * @param  Crawler $select
     *
     * @return array
     */
    private function getSelectedValueFromSelect(Crawler $select)
    {
        return $select->element()->selectedValues();
    }

    /**
     * Get the checked value from a radio group.
     *
     * @param  Crawler $radioGroup
     *
     * @return string|null
     */
    private function getCheckedValueFromRadioGroup(Crawler $radioGroup)
    {
        foreach ($radioGroup as $radio) {
            if ($radio->selected()) {
                return $radio->value();
            }
        }
    }

    /**
     * Returns the description of the failure.
     *
     * @return string
     */
    protected function getFailureDescription()
    {
        return sprintf(
            'the element [%s] has the selected value [%s]',
            $this->selector, $this->value
        );
    }

    /**
     * Returns the reversed description of the failure.
     *
     * @return string
     */
    protected function getReverseFailureDescription()
    {
        return sprintf(
            'the element [%s] does not have the selected value [%s]',
            $this->selector, $this->value
        );
    }
}

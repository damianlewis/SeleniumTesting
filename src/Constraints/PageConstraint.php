<?php

namespace SeleniumTesting\Constraints;

use SeleniumTesting\Crawler;
use PHPUnit_Framework_Constraint;
use SebastianBergmann\Comparator\ComparisonFailure;
use PHPUnit_Framework_ExpectationFailedException as FailedExpection;

abstract class PageConstraint extends PHPUnit_Framework_Constraint
{

    /**
     * Make sure we obtain the HTML from the crawler or the response.
     *
     * @param  Crawler|string $crawler
     *
     * @return string
     */
    protected function html($crawler)
    {
        return is_object($crawler) ? $crawler->html() : $crawler;
    }

    /**
     * Make sure we obtain the HTML from the crawler or the response.
     *
     * @param  Crawler|string $crawler
     *
     * @return string
     */
    protected function text($crawler)
    {
        return is_object($crawler) ? $crawler->text() : strip_tags($crawler);
    }

    /**
     * Create a crawler instance if the given value is not already a Crawler.
     *
     * @param  Crawler|string $crawler
     *
     * @return Crawler
     */
    protected function crawler($crawler)
    {
        return is_object($crawler) ? $crawler : new Crawler($crawler);
    }

    /**
     * Get the escaped text pattern for the constraint.
     *
     * @param  string $text
     *
     * @return string
     */
    protected function getEscapedPattern($text)
    {
        $rawPattern = preg_quote($text, '/');

        $escapedPattern = preg_quote(e($text), '/');

        return $rawPattern == $escapedPattern
            ? $rawPattern : "({$rawPattern}|{$escapedPattern})";
    }

    /**
     * Throw an exception for the given comparison and test description.
     *
     * @param  Crawler|string         $crawler
     * @param  string                 $description
     * @param  ComparisonFailure|null $comparisonFailure
     *
     * @return void
     *
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    protected function fail($crawler, $description, ComparisonFailure $comparisonFailure = null)
    {
        $failureDescription = sprintf(
            "Failed asserting that %s", $this->getFailureDescription()
        );

        if (! empty($description)) {
            $failureDescription .= ": {$description}";
        }

        throw new FailedExpection($failureDescription, $comparisonFailure);
    }

    /**
     * Get the description of the failure.
     *
     * @return string
     */
    protected function getFailureDescription()
    {
        return 'the page contains '.$this->toString();
    }

    /**
     * Returns the reversed description of the failure.
     *
     * @return string
     */
    protected function getReverseFailureDescription()
    {
        return 'the page does not contain '.$this->toString();
    }

    /**
     * Get a string representation of the object.
     *
     * Placeholder method to avoid forcing definition of this method.
     *
     * @return string
     */
    public function toString()
    {
        return '';
    }
}
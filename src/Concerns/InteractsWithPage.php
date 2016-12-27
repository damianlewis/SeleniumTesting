<?php

namespace SeleniumTesting\Concerns;

use SeleniumTesting\Constraints\HasInElement;
use SeleniumTesting\Constraints\HasLink;
use SeleniumTesting\Constraints\HasSource;
use SeleniumTesting\Constraints\HasText;
use SeleniumTesting\Constraints\HasValue;
use SeleniumTesting\Constraints\IsChecked;
use SeleniumTesting\Constraints\IsDisabled;
use SeleniumTesting\Constraints\IsSelected;
use SeleniumTesting\HttpException;
use SeleniumTesting\Constraints\ReversePageConstraint;
use SeleniumTesting\Crawler;
use SeleniumTesting\Constraints\HasElement;
use SeleniumTesting\Constraints\PageConstraint;
use PHPUnit_Framework_ExpectationFailedException as PHPUnitException;
use SeleniumTesting\InvalidArgumentException;
use PHPUnit_Extensions_Selenium2TestCase_WebDriverException as Selenium2TestCase_WebDriverException;

trait InteractsWithPage
{

    /**
     * The SeleniumTesting Crawler instance.
     *
     * @var Crawler
     */
    protected $crawler;

    /**
     * Visit the given URI.
     *
     * @param string $uri
     *
     * @return $this
     */
    public function visit($uri)
    {
        return $this->makeRequest($uri);
    }

    /**
     * Make a request to the given URI and create a crawler instance.
     *
     * @param string $uri
     *
     * @return $this
     */
    protected function makeRequest($uri)
    {
        $this->url($uri);

        $this->assertPageLoaded($uri);

        $this->crawler = new Crawler($this, $this->byXPath('//*'), $this->url());

        return $this;
    }

    /**
     *  Assert that the current page matches a given URI.
     *
     * @param string $uri
     *
     * @return $this
     */
    public function seePageIs($uri)
    {
        $this->assertEquals(
            $this->fullUrl($uri),
            $this->url(),
            "Did not land on expected page [{$uri}].\n"
        );

        return $this;
    }

    /**
     * Assert that a given uri has successfully loaded.
     *
     * @param  string      $uri
     * @param  string|null $message
     *
     * @return $this
     *
     * @throws HttpException
     */
    protected function assertPageLoaded($uri, $message = null)
    {
        $statusCode = $this->getStatusCode($uri);

        try {
            $this->assertContains($statusCode, ['200', '302']);
        } catch (PHPUnitException $e) {
            $message = $message ?: "A request to [{$uri}] failed. Received status code [{$statusCode}].";

            throw new HttpException($message);
        }

        return $this;
    }

    /**
     * Get the current crawler according to the test context.
     *
     * @return \SeleniumTesting\Crawler
     */
    protected function crawler()
    {
        return $this->crawler;
    }

    /**
     * Assert the given constraint for the page.
     *
     * @param PageConstraint $constraint
     * @param bool           $reverse
     * @param string         $message
     *
     * @return $this
     */
    protected function assertInPage(PageConstraint $constraint, $reverse = false, $message = '')
    {
        if ($reverse) {
            $constraint = new ReversePageConstraint($constraint);
        }

        self::assertThat($this->crawler(), $constraint, $message);

        return $this;
    }

    /**
     * Assert that a given string is seen on the current HTML.
     *
     * @param string $text
     * @param bool   $negate
     *
     * @return $this
     *
     */
    public function see($text, $negate = false)
    {
        return $this->assertInPage(new HasSource($text), $negate);
    }

    /**
     * Assert that a given string is not seen on the current HTML.
     *
     * @param string $text
     *
     * @return $this
     */
    public function dontSee($text)
    {
        return $this->assertInPage(new HasSource($text), true);
    }

    /**
     * Assert that an element is present on the page.
     *
     * @param string $selector
     * @param array  $attributes
     * @param int    $count
     * @param bool   $negate
     *
     * @return $this
     */
    public function seeElement($selector, array $attributes = [], $count = null, $negate = false)
    {
        return $this->assertInPage(new HasElement($selector, $attributes, $count), $negate);
    }

    /**
     * Assert that an element is not present on the page.
     *
     * @param string $selector
     * @param array  $attributes
     *
     * @return $this
     */
    public function dontSeeElement($selector, array $attributes = [])
    {
        return $this->assertInPage(new HasElement($selector, $attributes), true);
    }

    /**
     * Assert that a given string is seen on the current text.
     *
     * @param  string $text
     * @param  bool   $negate
     *
     * @return $this
     */
    public function seeText($text, $negate = false)
    {
        return $this->assertInPage(new HasText($text), $negate);
    }

    /**
     * Assert that a given string is not seen on the current text.
     *
     * @param  string $text
     *
     * @return $this
     */
    public function dontSeeText($text)
    {
        return $this->assertInPage(new HasText($text), true);
    }

    /**
     * Assert that a given string is seen within an element.
     *
     * @param string $element
     * @param string $text
     * @param bool   $negate
     *
     * @return $this
     */
    public function seeInElement($element, $text, $negate = false)
    {
        return $this->assertInPage(new HasInElement($element, $text), $negate);

        return $this;
    }

    /**
     * Assert that a given string is not seen within an element.
     *
     * @param string $element
     * @param string $text
     *
     * @return $this
     */
    public function dontSeeInElement($element, $text)
    {
        return $this->assertInPage(new HasInElement($element, $text), true);
    }

    /**
     * Assert that a given link is seen on the page.
     *
     * @param  string      $text
     * @param  string|null $url
     * @param  bool        $negate
     *
     * @return $this
     */
    public function seeLink($text, $url = null, $negate = false)
    {
        return $this->assertInPage(new HasLink($text, $url), $negate);
    }

    /**
     * Assert that a given link is not seen on the page.
     *
     * @param  string      $text
     * @param  string|null $url
     *
     * @return $this
     */
    public function dontSeeLink($text, $url = null)
    {
        return $this->assertInPage(new HasLink($text, $url), true);
    }

    /**
     * Assert that an input field contains the given value.
     *
     * @param  string $selector
     * @param  string $expected
     * @param  bool   $negate
     *
     * @return $this
     */
    public function seeInField($selector, $expected, $negate = false)
    {
        return $this->assertInPage(new HasValue($selector, $expected), $negate);
    }

    /**
     * Assert that an input field does not contain the given value.
     *
     * @param  string $selector
     * @param  string $value
     *
     * @return $this
     */
    public function dontSeeInField($selector, $value)
    {
        return $this->assertInPage(new HasValue($selector, $value), true);
    }

    /**
     * Assert that the expected value is selected.
     *
     * @param  string $selector
     * @param  string $value
     * @param  bool   $negate
     *
     * @return $this
     */
    public function seeIsSelected($selector, $value, $negate = false)
    {
        return $this->assertInPage(new IsSelected($selector, $value), $negate);
    }

    /**
     * Assert that the given value is not selected.
     *
     * @param  string $selector
     * @param  string $value
     *
     * @return $this
     */
    public function dontSeeIsSelected($selector, $value)
    {
        return $this->assertInPage(new IsSelected($selector, $value), true);
    }

    /**
     * Assert that the given checkbox is selected.
     *
     * @param  string $selector
     * @param  bool   $negate
     *
     * @return $this
     */
    public function seeIsChecked($selector, $negate = false)
    {
        return $this->assertInPage(new IsChecked($selector), $negate);
    }

    /**
     * Assert that the given checkbox is not selected.
     *
     * @param  string $selector
     *
     * @return $this
     */
    public function dontSeeIsChecked($selector)
    {
        return $this->assertInPage(new IsChecked($selector), true);
    }

    /**
     * Assert that the given form element is disabled.
     *
     * @param  string $selector
     * @param  bool   $negate
     *
     * @return $this
     */
    public function seeIsDisabled($selector, $negate = false)
    {
        return $this->assertInPage(new IsDisabled($selector), $negate);
    }

    /**
     * Assert that the given form element is not disabled.
     *
     * @param  string $selector
     *
     * @return $this
     */
    public function dontSeeIsDisabled($selector)
    {
        return $this->assertInPage(new IsDisabled($selector), true);
    }

    /**
     * Click a link with the given link text or id attribute.
     *
     * @param string $name
     *
     * @return $this
     */
    public function click($name)
    {
        $link = $this->crawler()->selectLink($name);

        if (! count($link)) {
            $link = $this->filterByNameOrId($name, 'a');

            if (! count($link)) {
                throw new InvalidArgumentException(
                    "Could not find a link with the text, name, or ID attribute of [{$name}]."
                );
            }
        }

        $link->element()->click();

        return $this;
    }

    /**
     * Fill a text field with the given text.
     *
     * @param string $text
     * @param string $element
     *
     * @return $this
     */
    public function type($text, $element)
    {
        $textField = $this->filterByNameOrId($element, 'input,textarea');

        if (! count($textField)) {
            throw new InvalidArgumentException("Could not find any text field elements with a name or ID attribute of [{$element}].");
        }

        $textField->element()->value($text);

        return $this;
    }

    /**
     * Clear a text field.
     *
     * @param string $element
     *
     * @return $this
     */
    public function clear($element)
    {
        $textField = $this->filterByNameOrId($element, 'input,textarea');

        if (! count($textField)) {
            throw new InvalidArgumentException("Could not find any text field elements with a name or ID attribute of [{$element}].");
        }

        $textField->element()->clear();

        return $this;
    }

    /**
     * Check a checkbox on the page.
     *
     * @param string $element
     * @param bool   $negate
     *
     * @return $this
     */
    protected function check($element, $negate = false)
    {
        $checkbox = $this->filterByNameOrId($element, 'input[type="checkbox"]');

        if (! count($checkbox)) {
            throw new InvalidArgumentException("Could not find any checkbox elements with a name or ID attribute of [{$element}].");
        }

        if ($checkbox->element()->selected() == $negate) {
            $checkbox->element()->click();
        } else {
            throw new InvalidArgumentException(sprintf("Checkbox element [%s] is already %s.", $element, $negate ? 'unchecked' : 'checked'));
        }

        return $this;
    }

    /**
     * Uncheck a checkbox on the page.
     *
     * @param  string $element
     *
     * @return $this
     */
    protected function uncheck($element)
    {
        return $this->check($element, true);
    }

    /**
     * Select an option from a drop-down.
     *
     * @param  string  $label
     * @param  string  $element
     * @return $this
     */
    protected function select($label, $element)
    {
        $select = $this->filterByNameOrId($element, 'select');

        if (! count($select)) {
            throw new InvalidArgumentException("Could not find any select elements with a name or ID attribute of [{$element}].");
        }

        try {
            $select->element()->selectOptionByLabel($label);
        } catch (Selenium2TestCase_WebDriverException $exception) {
            throw new InvalidArgumentException("The option labelled [{$label}] is not present on the drop down list.");
        }

        return $this;
    }

    /**
     * Press a button with the given button text, name or ID attribute.
     *
     * @param string $name
     *
     * @return $this
     */
    public function press($name)
    {
        $button = $this->crawler()->selectButton($name);

        if (! count($button)) {
            $button = $this->filterByNameOrId($name, 'button');

            if (! count($button)) {
                throw new InvalidArgumentException(
                    "Could not find a button with the text, name, or ID attribute of [{$name}]."
                );
            }
        }

        $button->element()->click();

        return $this;
    }

    /**
     * Click an element with the given text value or ID attribute.
     *
     * @param string $name
     *
     * @return $this
     */
    public function clickOnElement($name)
    {
        $element = $this->crawler()->selectElement($name);

        if (! count($element)) {
            $element = $this->filterByNameOrId($name);

            if (! count($element)) {
                throw new InvalidArgumentException(
                    "Could not find an element with the text, name, or ID attribute of [{$name}]."
                );
            }
        }

        $element->element()->click();

        return $this;
    }

//    /**
//     * Filter elements according to the given attributes.
//     *
//     * @param string       $name
//     * @param array        $attributes
//     * @param array|string $elements
//     *
//     * @return \SeleniumTesting\Crawler
//     */
//    protected function filterByAttributes($name, array $attributes, $elements = '*')
//    {
////        $name = str_replace('#', '', $name);
//
////        $id = str_replace(['[', ']'], ['\\[', '\\]'], $name);
//
//        $selectors = [];
//
//        $elements = is_array($elements) ? $elements : [$elements];
//
//        foreach ($elements as $element) {
//            foreach ($attributes as $attribute) {
//                $selectors[] = "//{$element}[@{$attribute}='{$name}']";
//            }
//        }
//
////        array_walk($elements, function (&$element) use ($name, $id) {
////            $element = "//{$element}[@{}], {$element}[name='{$name}']";
////        });
//
//        return $this->crawler()->filter($selectors);
//    }

    /**
     * Filter elements according to the given name or ID attribute.
     *
     * @param string       $name
     * @param array|string $elements
     *
     * @return Crawler
     */
    protected function filterByNameOrId($name, $elements = '*')
    {
        return $this->crawler()->filter(implode(', ', $this->getElements($name, $elements)));
    }

    /**
     * Get the elements for the filter.
     *
     * @param string $name
     * @param string $elements
     *
     * @return array
     */
    protected function getElements($name, $elements)
    {
        $name = ltrim($name, '#');

        return collect(explode(',', $elements))->map(function ($element) use ($name) {
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

    /**
     * Return the full url for the given uri.
     *
     * @param string $uri
     *
     * @return string
     */
    protected function fullUrl($uri)
    {
        return implode('/', [rtrim($this->baseUrl, '/'), trim($uri, '/')]);
    }

    /**
     * Get the status code for the given uri.
     *
     * @param string $uri
     *
     * @return string
     */
    protected function getStatusCode($uri)
    {
        $headers = get_headers($this->fullUrl($uri));

        return substr($headers[0], 9, 3);
    }
}
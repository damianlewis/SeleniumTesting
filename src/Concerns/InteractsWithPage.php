<?php

namespace SeleniumTesting\Concerns;

use SeleniumTesting\Constraints\HasInElement;
use SeleniumTesting\Constraints\HasLink;
use SeleniumTesting\Constraints\HasSource;
use SeleniumTesting\Constraints\HasText;
use SeleniumTesting\HttpException;
use SeleniumTesting\Constraints\ReversePageConstraint;
use SeleniumTesting\Crawler;
use SeleniumTesting\Constraints\HasElement;
use SeleniumTesting\Constraints\PageConstraint;
use PHPUnit_Framework_ExpectationFailedException as PHPUnitException;

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

//    /**
//     * Click a link with the given link text or id attribute.
//     *
//     * @param string $name
//     *
//     * @return $this
//     */
//    public function click($name)
//    {
////        $link = $this->crawler()->link($name);
//        $link = $this->crawler()->selectLink($name);
//
//        if (! count($link)) {
//            $link = $this->filterByNameOrId($name, 'a');
//
//            if (! count($link)) {
//                throw new InvalidArgumentException(
//                    "Could not find a link with a body, name, or ID attribute of [{$name}]."
//                );
//            }
//        }
//
//        if (! $link) {
//            throw new InvalidArgumentException("Could not find a link with the text or id attribute of [{$name}].");
//        }
//
//        $link->click();
//
//        return $this;
//    }

//    /**
//     * Fill a text field with the given text.
//     *
//     * @param string $text
//     * @param string $element
//     *
//     * @return $this
//     */
//    public function type($text, $element)
//    {
//        $textField = $this->crawler()->selectTextField($element);
//
//        if (! $textField) {
//            throw new InvalidArgumentException("Could not find any text field elements with a name or ID attribute of [{$element}].");
//        }
//
//        $textField->value($text);
//
//        return $this;
//    }

//    /**
//     * Press a button with the given button text, name or ID attribute.
//     *
//     * @param string $name
//     *
//     * @return $this
//     */
//    public function press($name)
//    {
//        $button = $this->crawler()->selectButton($name);
//
//        if (! $button) {
//            throw new InvalidArgumentException("Could not find a button with the text, name or ID attribute of [{$name}].");
//        }
//
//        $button->click();
//
//        return $this;
//    }

//    /**
//     * Click an element with the given text value or ID attribute.
//     *
//     * @param string $name
//     *
//     * @return $this
//     */
//    public function clickOnElement($name)
//    {
//        $element = $this->crawler()->selectElementToClick($name);
//
//        if (! $element) {
//            throw new InvalidArgumentException("Could not find an element with the text or ID attribute of [{$name}].");
//        }
//
//        $element->click();
//
//        return $this;
//    }

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
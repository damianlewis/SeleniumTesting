<?php

namespace SeleniumTesting;

use ArrayIterator;
use Closure;
use Countable;
use IteratorAggregate;
use SeleniumTestCase;
use PHPUnit_Extensions_Selenium2TestCase_Element as Selenium2TestCase_Element;

class Crawler implements Countable, IteratorAggregate
{

    private $document;
    private $elements = [];

    /**
     * Create a new SeleniumTestCase crawler.
     *
     * @param Selenium2TestCase $document
     * @param mixed|null        $node
     */
    public function __construct($document, $node = null)
    {
        $this->add($document);
        $this->add($node);
    }

    /**
     * Removes all the elements.
     */
    public function clear()
    {
        $this->document = null;
        $this->elements = [];
    }

    /**
     *  Adds the current document to crawl through.
     *
     * @param mixed|null $node
     *
     * @throws InvalidArgumentException
     */
    public function add($node)
    {
        if ($node instanceof SeleniumTestCase) {
            $this->addDocument($node);
        } elseif ($node instanceof Selenium2TestCase_Element) {
            $this->addElement($node);
        } elseif (is_array($node)) {
            $this->addElements($node);
        } elseif ($node !== null) {
            throw new InvalidArgumentException(sprintf('Expecting a SeleniumTestCase instance, PHPUnit_Extensions_Selenium2TestCase_Element instance, array or null, but got "%s".',
                is_object($node) ? get_class($node) : gettype($node)));
        }
    }

    /**
     * Adds the SeleniumTestCase document containing the DOM.
     *
     * @param SeleniumTestCase $document
     */
    public function addDocument(SeleniumTestCase $document)
    {
        $this->document = $document;
    }

    /**
     * Adds an array of Selenium2TestCase Element instances to the list of elements.
     *
     * @param array $elements
     */
    public function addElements(array $elements)
    {
        foreach ($elements as $element) {
            $this->add($element);
        }
    }

    /**
     * Adds a Selenium2TestCase Element instance to the list of elements.
     *
     * @param Selenium2TestCase_Element $element
     */
    public function addElement(Selenium2TestCase_Element $element)
    {
        // Don't add duplicate elements in the Crawler
        if (in_array($element, $this->elements, true)) {
            return;
        }

        $this->elements[] = $element;
    }

    /**
     * Returns the text from the child nodes of the first element from the list.
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    public function text()
    {
        if (empty($this->elements)) {
            throw new InvalidArgumentException('The current element list is empty.');
        }

        return $this->getElement(0)->text();
    }

    /**
     * Returns the first element from the list as HTML.
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    public function html()
    {
        if (empty($this->elements)) {
            throw new InvalidArgumentException('The current element list is empty.');
        }

        return $this->getElement(0)->attribute('innerHTML');
    }

    /**
     * Filters the document by the given css selector, id, name, class attribute or tag.
     *
     * @param mixed $selectors
     *
     * @return Crawler
     */
    public function filter($selectors)
    {
        $crawler = $this->createSubCrawler($this->document, null);

//        if (is_string($selector)) {
//            $selector = [$selector];
//        }

        $selectors = is_array($selectors) ? $selectors : [$selectors];

        foreach ($selectors as $selector) {
            if (starts_with($selector, '//')) {
                $elements = $this->filterByXPath($selector);
            } elseif (starts_with($selector, '#') || starts_with($selector, '.')) {
                $elements = $this->filterByCss($selector);
            } else {
                $elements = $this->filterById($selector);

                if (empty($elements)) {
                    $elements = $this->filterByName($selector);
                }

                if (empty($elements)) {
                    $elements = $this->filterByClass($selector);
                }

                if (empty($elements)) {
                    $elements = $this->filterByTag($selector);
                }
            }

            $crawler->add($elements);
        }

        return $crawler;
    }

//    /**
//     * Select any elements that match the given tag name.
//     * If no name is provided all elements in the document are returned.
//     *
//     * @param string $name
//     *
//     * @return array
//     */
//    public function selectElements($name = '*')
//    {
//        return $this->filterByXPath("//{$name}");
//    }

//    /**
//     * Select any html elements by the given attributes where the attributes match the given name.
//     * The selected elements can be filtered by the given constraints (tag names).
//     *
//     * @param string $name
//     * @param array  $attributes
//     * @param array  $constraints
//     *
//     * @return array
//     */
//    protected function selectElementsByAttribute($name, array $attributes = [], array $constraints = [])
//    {
//        $elements = [];
//
//        if (empty($attributes)) {
//            return $elements;
//        }
//
//        if (empty($constraints)) {
//            $elements = $this->selectElementsByAttribute($name, $attributes, ['*']);
//        }
//
//        foreach ($constraints as $constraint) {
//            if (starts_with($name, '#') || starts_with($name, '.')) {
//                $name = $this->stripCssSelectorCharacter($name);
//            }
//
//            foreach ($attributes as $attribute) {
//                if (empty($elements)) {
//                    $elements = $this->filterByXPath("//{$constraint}[@{$attribute}='{$name}']");
//                }
//            }
//        }
//
//        return $elements;
//    }

//    /**
//     * Select any html elements by the given text.
//     * The selected elements can be filtered by:
//     * - The given constraints (tag names)
//     * - The given attributes where the attributes match the given name.
//     *
//     * @param string      $text
//     * @param array       $constraints
//     * @param string|null $name
//     * @param array       $attributes
//     *
//     * @return array
//     */
//    protected function selectElementsWithText($text, array $constraints = [], $name = null, array $attributes = [])
//    {
//        $elements = [];
//
//        if (empty($constraints)) {
//            $elements = $this->selectElementsWithText($text, ['*'], $name, $attributes);
//        }
//
//        foreach ($constraints as $constraint) {
//            if (starts_with($name, '#') || starts_with($name, '.')) {
//                $name = $this->stripCssSelectorCharacter($name);
//            }
//
//            if (empty($attributes)) {
//                $elements = $this->filterByXPath("//{$constraint}[contains(text(),'{$text}')]");
//            } else {
//                if (is_null($name)) {
//                    return $elements;
//                }
//
//                foreach ($attributes as $attribute) {
//                    if (empty($elements)) {
//                        $elements = $this->filterByXPath("//{$constraint}[@{$attribute}='{$name}'][contains(text(),'{$text}')]]");
//                    }
//                }
//            }
//
//        }
//
//        return $elements;
//    }

    /**
     * Selects links by the given link text.
     *
     * @param string $name
     *
     * @return Crawler
     */
    public function selectLink($name)
    {
        $links = $this->createSubCrawler($this->document, null);

//        $elements = $this->filterByLinkText($name);

//        if (empty($elements)) {
//            $elements = $this->selectElementsByAttribute($name, ['id'], ['a']);
//        }

        $links->add($this->filterByLinkText($name));

        return $links;
    }

//    /**
//     * Select any html buttons by the given button text, name or id attribute.
//     *
//     * @param string $name
//     *
//     * @return array
//     */
//    public function selectButtons($name)
//    {
//        $buttons = $this->selectElementsWithText($name, ['button']);
//
//        if (empty($buttons)) {
//            $buttons = $this->selectElementsByAttribute($name, ['name', 'id'], ['button']);
//        }
//
//        return $buttons;
//    }

//    /**
//     * Select any html elements by the given element text or id attribute.
//     *
//     * @param string $name
//     *
//     * @return array
//     */
//    public function selectElementsByTextOrId($name)
//    {
//        $elements = $this->selectElementsWithText($name);
//
//        if (empty($elements)) {
//            $elements = $this->selectElementsByAttribute($name, ['id']);
//        }
//
//        return $elements;
//    }

//    /**
//     * Select any html elements by the given body text, id or class attribute.
//     *
//     * @param string $name
//     * @param array  $constraints
//     *
//     * @return mixed|null
//     *
//     */
//    public function selectElements(string $name, array $constraints = [])
//    {
//        $elements = [];
//
//        if (empty($constraints)) {
//            $elements = $this->selectElementsByNameOrId($name, ['*']);
//        } else {
//            foreach ($constraints as $constraint) {
//                if (starts_with($name, '#') || starts_with($name, '.')) {
//                    $name = $this->stripCssSelectorCharacter($name);
//                }
//
//                $elements = $this->filterByXPath("//{$constraint}[contains(text(), '{$name}')]");
//
//                if (empty($elements)) {
//                    $elements = $this->filterByXPath("//{$constraint}[@id='{$name}']");
//                }
//
//                if (empty($elements)) {
//                    $elements = $this->filterByXPath("//{$constraint}[@class='{$name}']");
//                }
//            }
//        }
//
//
//        return $elements;
//    }

//    /**
//     * Select any html form elements by the given body text, name, id or class attribute.
//     *
//     * @param string $name
//     * @param array  $constraints
//     *
//     * @return mixed|null
//     *
//     */
//    public function selectFormElements(string $name, array $constraints = [], string $type = '')
//    {
//        $elements = [];
//
//        if (empty($constraints)) {
//            $elements = $this->selectElementsByNameOrId($name, ['*']);
//        } else {
//            foreach ($constraints as $constraint) {
//                if (starts_with($name, '#') || starts_with($name, '.')) {
//                    $elements = $this->filterByCss($name);
//                } else {
//                    if (empty($elements)) {
//                        $elements = $this->filterByXPath("//{$constraint}[@name='{$name}']");
//                    }
//
//                    if (empty($elements)) {
//                        $elements = $this->filterByXPath("//{$constraint}[@id='{$name}']");
//                    }
//
//                    if (empty($elements)) {
//                        $elements = $this->filterByXPath("//{$constraint}[@class='{$name}']");
//                    }
//                }
//            }
//        }
//
//        return $elements;
//    }


//    /**
//     * Select the first html link by the given selector name.
//     *
//     * @param string $name
//     *
//     * @return \PHPUnit_Extensions_Selenium2TestCase_Element
//     */
//    public function link($name)
//    {
//        return array_first($this->selectLink($name));
//    }

//    /**
//     * Select the first text field element by the given selector name.
//     *
//     * @param string $name
//     *
//     * @return \PHPUnit_Extensions_Selenium2TestCase_Element
//     */
//    public function selectTextField($name)
//    {
//        return array_first($this->selectElementsByAttribute($name, ['name', 'id'], ['input', 'textarea']));
//    }

//    /**
//     * Select the first html button by the given selector name.
//     *
//     * @param string $name
//     *
//     * @return \PHPUnit_Extensions_Selenium2TestCase_Element
//     */
//    public function selectButton($name)
//    {
//        return array_first($this->selectButtons($name));
//    }

//    /**
//     * Select the first element (for clicking) by the given selector name.
//     *
//     * @param string $name
//     *
//     * @return \PHPUnit_Extensions_Selenium2TestCase_Element
//     */
//    public function selectElementToClick($name)
//    {
//        return array_first($this->selectElementsByTextOrId($name));
//    }

    /**
     * @param int $position
     *
     * @return Selenium2TestCase_Element|null
     */
    public function getElement($position)
    {
        if (isset($this->elements[$position])) {
            return $this->elements[$position];
        }
    }

    /**
     * Return the number of filter nodes.
     *
     * @return int
     */
    public function count()
    {
        return count($this->elements);
    }

    /**
     * Return an array iterator for the nodes.
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->elements);
    }

    /**
     * Reduces the list of elements by calling an anonymous function.
     * To remove an element from the list, the anonymous function must return false.
     *
     * @param Closure $closure
     *
     * @return Crawler
     */
    public function reduce(Closure $closure)
    {
        $elements = [];

        foreach ($this->elements as $element) {
            if ($closure($element) !== false) {
                $elements[] = $element;
            }
        }

        return $this->createSubCrawler($elements);
    }

    /**
     * Filter the elements using an xPath descriptor.
     *
     * @param string $selector
     *
     * @return array
     */
    protected function filterByXPath($selector)
    {
        $elements = $this->document->elements($this->document->using('xpath')->value($selector));

        return $elements;
    }

    /**
     * Filter the elements by css selector.
     *
     * @param string $selector
     *
     * @return array
     */
    protected function filterByCss($selector)
    {
        $elements = $this->document->elements($this->document->using('css selector')->value($selector));

        return $elements;
    }

    /**
     * Filter the elements by id attribute.
     *
     * @param string $selector
     *
     * @return array
     */
    protected function filterById($selector)
    {
        $elements = $this->document->elements($this->document->using('id')->value($selector));

        return $elements;
    }

    /**
     * Filter the elements by name attribute.
     *
     * @param string $selector
     *
     * @return array
     */
    protected function filterByName($selector)
    {
        $elements = $this->document->elements($this->document->using('name')->value($selector));

        return $elements;
    }

    /**
     * Filter the elements by class attribute.
     *
     * @param string $selector
     *
     * @return array
     */
    protected function filterByClass($selector)
    {
        $elements = $this->document->elements($this->document->using('class name')->value($selector));

        return $elements;
    }

    /**
     * Filter the elements by tag name.
     *
     * @param string $selector
     *
     * @return array
     */
    protected function filterByTag($selector)
    {
        $elements = $this->document->elements($this->document->using('tag name')->value($selector));

        return $elements;
    }

    /**
     * Filter the elements by link text.
     *
     * @param string $text
     *
     * @return array
     */
    protected function filterByLinkText($text)
    {
        $elements = $this->document->elements($this->document->using('link text')->value($text));

        return $elements;
    }

//    /**
//     * Strip out any leading css selector characters.
//     *
//     * @param string $selector
//     *
//     * @return string
//     */
//    protected function stripCssSelectorCharacter($selector)
//    {
//        return ltrim($selector, '#.');
//    }

    /**
     * Creates a crawler for some elements.
     *
     * @param array $elements
     *
     * @return Crawler
     */
    protected function createSubCrawler($elements)
    {
        $crawler = new static($this->document, $elements);

        return $crawler;
    }
}
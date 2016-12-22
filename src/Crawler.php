<?php

namespace SeleniumTesting;

use ArrayIterator;
use Closure;
use Countable;
use IteratorAggregate;
use PHPUnit_Extensions_Selenium2TestCase as Selenium2TestCase;

class Crawler implements Countable, IteratorAggregate
{

    protected $uri;
    private $baseHref;
    private $document;
    private $nodes = [];

    /**
     * Create a new SeleniumTestCase crawler.
     *
     * @param mixed|null $node
     * @param mixed|null $currentUri
     * @param mixed|null $baseHref
     */
    public function __construct($node = null, $currentUri = null, $baseHref = null)
    {
        $this->uri = $currentUri;
        $this->baseHref = $baseHref ?: $currentUri;

        $this->add($node);
    }

    /**
     * Removes all the nodes.
     */
    public function clear()
    {
        $this->nodes = [];
        $this->document = null;
    }

    /**
     * Returns the current URI.
     *
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Returns base href.
     *
     * @return string
     */
    public function getBaseHref()
    {
        return $this->baseHref;
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
        if ($node instanceof Selenium2TestCase) {
            $this->document = $node;
        } elseif (is_array($node)) {
            $this->nodes = $node;
        } elseif ($node !== null) {
            throw new InvalidArgumentException(sprintf('Expecting a PHPUnit_Extensions_Selenium2TestCase instance, array or null, but got "%s".',
                is_object($node) ? get_class($node) : gettype($node)));
        }
    }

    /**
     * Filters the document by the given css selector, id, name, class attribute or tag.
     *
     * @param mixed $selector
     *
     * @return Crawler
     */
    public function filter($selector)
    {
        $crawler = $this->createSubCrawler(null);

        if (is_string($selector)) {
            $selector = [$selector];
        }

        foreach ($selector as $query) {
            $nodes = [];

            if (starts_with($query, '//')) {
                $nodes = $this->filterByXPath($query);
            } elseif (starts_with($query, '#') || starts_with($query, '.')) {
                $nodes = $this->filterByCss($query);
            } else {
                $nodes = $this->filterById($query);

                if (empty($nodes)) {
                    $nodes = $this->filterByName($query);
                }

                if (empty($nodes)) {
                    $nodes = $this->filterByClass($query);
                }

                if (empty($nodes)) {
                    $nodes = $this->filterByTag($query);
                }
            }

            $crawler->nodes = array_merge($crawler->nodes, $nodes);
        }

        return $crawler;
    }

    /**
     * Select any elements that match the given tag name.
     * If no name is provided all elements in the document are returned.
     *
     * @param string $name
     *
     * @return array
     */
    public function selectElements($name = '*')
    {
        return $this->filterByXPath("//{$name}");
    }

    /**
     * Select any html elements by the given attributes where the attributes match the given name.
     * The selected elements can be filtered by the given constraints (tag names).
     *
     * @param string $name
     * @param array  $attributes
     * @param array  $constraints
     *
     * @return array
     */
    public function selectElementsByAttribute($name, array $attributes = [], array $constraints = [])
    {
        $elements = [];

        if (empty($attributes)) {
            return $elements;
        }

        if (empty($constraints)) {
            $elements = $this->selectElementsByAttribute($name, $attributes, ['*']);
        }

        foreach ($constraints as $constraint) {
            if (starts_with($name, '#') || starts_with($name, '.')) {
                $name = $this->stripCssSelectorCharacter($name);
            }

            foreach ($attributes as $attribute) {
                if (empty($elements)) {
                    $elements = $this->filterByXPath("//{$constraint}[@{$attribute}='{$name}']");
                }
            }
        }

        return $elements;
    }

    /**
     * Select any html elements by the given text.
     * The selected elements can be filtered by:
     * - The given constraints (tag names)
     * - The given attributes where the attributes match the given name.
     *
     * @param string      $text
     * @param array       $constraints
     * @param string|null $name
     * @param array       $attributes
     *
     * @return array
     */
    public function selectElementsWithText($text, array $constraints = [], $name = null, array $attributes = [])
    {
        $elements = [];

        if (empty($constraints)) {
            $elements = $this->selectElementsWithText($text, ['*'], $name, $attributes);
        }

        foreach ($constraints as $constraint) {
            if (starts_with($name, '#') || starts_with($name, '.')) {
                $name = $this->stripCssSelectorCharacter($name);
            }

            if (empty($attributes)) {
                $elements = $this->filterByXPath("//{$constraint}[contains(text(),'{$text}')]");
            } else {
                if (is_null($name)) {
                    return $elements;
                }

                foreach ($attributes as $attribute) {
                    if (empty($elements)) {
                        $elements = $this->filterByXPath("//{$constraint}[@{$attribute}='{$name}'][contains(text(),'{$text}')]]");
                    }
                }
            }

        }

        return $elements;
    }

    /**
     * Select any html links by the given link text, id or class attribute.
     *
     * @param string $name
     *
     * @return array
     */
    public function selectLinks($name)
    {
        $links = $this->filterByLinkText($name);

        if (empty($links)) {
            $links = $this->selectElementsByAttribute($name, ['id'], ['a']);
        }

        return $links;
    }

    /**
     * Select any html buttons by the given button text, name or id attribute.
     *
     * @param string $name
     *
     * @return array
     */
    public function selectButtons($name)
    {
        $buttons = $this->selectElementsWithText($name, ['button']);

        if (empty($buttons)) {
            $buttons = $this->selectElementsByAttribute($name, ['name', 'id'], ['button']);
        }

        return $buttons;
    }

    /**
     * Select any html elements by the given element text or id attribute.
     *
     * @param string $name
     *
     * @return array
     */
    public function selectElementsByTextOrId($name)
    {
        $elements = $this->selectElementsWithText($name);

        if (empty($elements)) {
            $elements = $this->selectElementsByAttribute($name, ['id']);
        }

        return $elements;
    }

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


    /**
     * Select the first html link by the given selector name.
     *
     * @param string $name
     *
     * @return \PHPUnit_Extensions_Selenium2TestCase_Element
     */
    public function selectLink($name)
    {
        return array_first($this->selectLinks($name));
    }

    /**
     * Select the first text field element by the given selector name.
     *
     * @param string $name
     *
     * @return \PHPUnit_Extensions_Selenium2TestCase_Element
     */
    public function selectTextField($name)
    {
        return array_first($this->selectElementsByAttribute($name, ['name', 'id'], ['input', 'textarea']));
    }

    /**
     * Select the first html button by the given selector name.
     *
     * @param string $name
     *
     * @return \PHPUnit_Extensions_Selenium2TestCase_Element
     */
    public function selectButton($name)
    {
        return array_first($this->selectButtons($name));
    }

    /**
     * Select the first element (for clicking) by the given selector name.
     *
     * @param string $name
     *
     * @return \PHPUnit_Extensions_Selenium2TestCase_Element
     */
    public function selectElementToClick($name)
    {
        return array_first($this->selectElementsByTextOrId($name));
    }

    /**
     * Return the number of filter nodes.
     *
     * @return int
     */
    public function count()
    {
        return count($this->nodes);
    }

    /**
     * Return an array iterator for the nodes.
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->nodes);
    }

    /**
     * Reduces the list of nodes by calling an anonymous function.
     * To remove a node from the list, the anonymous function must return false.
     *
     * @param Closure $closure
     *
     * @return Crawler
     */
    public function reduce(Closure $closure)
    {
        $nodes = [];

        foreach ($this->nodes as $node) {
            if ($closure($node) !== false) {
                $nodes[] = $node;
            }
        }

        return $this->createSubCrawler($nodes);
    }

    /**
     * Filter the elements using an xPath descriptor.
     *
     * @param string $selector
     *
     * @return array
     */
    private function filterByXPath($selector)
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
    private function filterByCss($selector)
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
    private function filterById($selector)
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
    private function filterByName($selector)
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
    private function filterByClass($selector)
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
    private function filterByTag($selector)
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
    private function filterByLinkText($text)
    {
        $elements = $this->document->elements($this->document->using('link text')->value($text));

        return $elements;
    }

    /**
     * Strip out any leading css selector characters.
     *
     * @param string $selector
     *
     * @return string
     */
    private function stripCssSelectorCharacter($selector)
    {
        $selector = ltrim($selector, '#');
        $selector = ltrim($selector, '.');

        return $selector;
    }

    /**
     * Creates a crawler for some subnodes.
     *
     * @param array $nodes
     *
     * @return Crawler
     */
    private function createSubCrawler($nodes)
    {
        $crawler = new static($nodes, $this->uri, $this->baseHref);
        $crawler->document = $this->document;

        return $crawler;
    }
}
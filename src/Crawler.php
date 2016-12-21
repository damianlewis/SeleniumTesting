<?php

namespace SeleniumTesting;

use ArrayIterator;
use Countable;
use InvalidArgumentException;
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
     * @param null $node
     * @param null $currentUri
     * @param null $baseHref
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
     * @param $node
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
     * @return $this
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
            }

            if (empty($nodes)) {
                $nodes = $this->filterByCss($query);
            }

            if (empty($nodes)) {
                $nodes = $this->filterById($query);
            }

            if (empty($nodes)) {
                $nodes = $this->filterByName($query);
            }

            if (empty($nodes)) {
                $nodes = $this->filterByClass($query);
            }

            if (empty($nodes)) {
                $nodes = $this->filterByTag($query);
            }

            $crawler->nodes = array_merge($crawler->nodes, $nodes);
        }

        return $crawler;
    }

    /**
     * Select any html links by the given link text, id or name attribute.
     *
     * @param string $name
     *
     * @return array|mixed
     */
    public function selectLinks(string $name)
    {
        $links = [];

        $query = $this->formatQuery($name);

        if (empty($links)) {
            $links = $this->filterByLinkText($name);
        }

        if (empty($links)) {
            $links = $this->filterByXPath("//a[@id='{$query}']");
        }

        if (empty($links)) {
            $links = $this->filterByXPath("//a[@name='{$query}']");
        }

        return $links;
    }

    /**
     * Select any html buttons by the given button text, id or name attribute.
     *
     * @param string $name
     *
     * @return array|null
     */
    public function selectButtons(string $name)
    {
        $buttons = [];

        $query = $this->formatQuery($name);

        if (empty($buttons)) {
            $buttons = $this->filterByXPath("//button[contains(text(), '{$query}')]");
        }

        if (empty($buttons)) {
            $buttons = $this->filterByXPath("//button[@id='{$query}']");
        }

        if (empty($buttons)) {
            $buttons = $this->filterByXPath("//button[@name='{$query}']");
        }

        return $buttons;
    }

    /**
     * Select any html elements by the given name or id attribute.
     *
     * @param string $name
     * @param array  $constraints
     *
     * @return mixed|null
     *
     */
    public function selectElementsByNameOrId(string $name, array $constraints = [])
    {
        $elements = [];

        $query = $this->formatQuery($name);

        if (empty($constraints)) {
            $elements = $this->selectElementsByNameOrId($name, ['*']);
        } else {
            foreach ($constraints as $constraint) {
                if (empty($elements)) {
                    $elements = $this->filterByXPath("//{$constraint}[@name='{$query}']");
                }

                if (empty($elements)) {
                    $elements = $this->filterByXPath("//{$constraint}[@id='{$query}']");
                }
            }
        }

        return $elements;
    }

    /**
     * Select any html elements by the given element text or id attribute.
     *
     * @param string $name
     * @param array  $constraints
     *
     * @return mixed|null
     */
    public function selectElementsByTextOrId(string $name, array $constraints = [])
    {
        $elements = [];

        $query = $this->formatQuery($name);

        if (empty($constraints)) {
            $elements = $this->selectElementsByTextOrId($name, ['*']);
        } else {
            foreach ($constraints as $constraint) {
                if (empty($elements)) {
                    $elements = $this->filterByXPath("//{$constraint}[contains(text(), '{$query}')]");
                }

                if (empty($elements)) {
                    $elements = $this->filterByXPath("//{$constraint}[@id='{$query}']");
                }
            }
        }

        return $elements;
    }

    /**
     * Select the first html link by the given link text, id or name attribute.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function selectLink(string $name)
    {
        return array_first($this->selectLinks($name));
    }

    /**
     * Select the first html button by the given link text, id or name attribute.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function selectButton(string $name)
    {
        return array_first($this->selectButtons($name));
    }

    /**
     * Select the first element by the given element text or id attribute.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function selectElement(string $name)
    {
        return array_first($this->selectElementsByTextOrId($name));
    }

    /**
     * Select the first text input field element by the given name or id attribute.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function selectInput(string $name)
    {
        return array_first($this->selectElementsByNameOrId($name, ['input']));
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
     *
     * To remove a node from the list, the anonymous function must return false.
     *
     * @param \Closure $closure
     *
     * @return Crawler
     */
    public function reduce(\Closure $closure)
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
     * Filter the elements by id attribute.
     *
     * @param string $selector
     *
     * @return mixed
     */
    private function filterById(string $selector)
    {
        $elements = $this->document->elements($this->document->using('id')->value($selector));

        return $elements;
    }

    /**
     * Filter the elements by class attribute.
     *
     * @param $selector
     *
     * @return mixed
     */
    private function filterByClass(string $selector)
    {
        $elements = $this->document->elements($this->document->using('class name')->value($selector));

        return $elements;
    }

    /**
     * Filter the elements using an xPath descriptor.
     *
     * @param $selector
     *
     * @return mixed
     */
    private function filterByXPath(string $selector)
    {
        $elements = $this->document->elements($this->document->using('xpath')->value($selector));

        return $elements;
    }

    /**
     * Filter the elements by css selector.
     *
     * @param $selector
     *
     * @return mixed
     */
    private function filterByCss(string $selector)
    {
        $elements = $this->document->elements($this->document->using('css selector')->value($selector));

        return $elements;
    }

    /**
     * Filter the elements by name attribute.
     *
     * @param $selector
     *
     * @return mixed
     */
    private function filterByName(string $selector)
    {
        $elements = $this->document->elements($this->document->using('name')->value($selector));

        return $elements;
    }

    /**
     * Filter the elements by tag name.
     *
     * @param $selector
     *
     * @return mixed
     */
    private function filterByTag(string $selector)
    {
        $elements = $this->document->elements($this->document->using('tag name')->value($selector));

        return $elements;
    }

    /**
     * Filter the elements by link text.
     *
     * @param string $text
     *
     * @return mixed
     */
    private function filterByLinkText(string $text)
    {
        $elements = $this->document->elements($this->document->using('link text')->value($text));

        return $elements;
    }

    /**
     * Strip out any leading hash or period characters from the query.
     *
     * @param $selector
     *
     * @return string
     */
    private function formatQuery($selector)
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
     * @return static
     */
    private function createSubCrawler($nodes)
    {
        $crawler = new static($nodes, $this->uri, $this->baseHref);
        $crawler->document = $this->document;

        return $crawler;
    }
}
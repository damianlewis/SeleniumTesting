<?php

namespace SeleniumTesting;

use ArrayIterator;
use Closure;
use Countable;
use IteratorAggregate;
use SeleniumTestCase;
use PHPUnit_Extensions_Selenium2TestCase_Element as Selenium2TestCase_Element;
use PHPUnit_Extensions_Selenium2TestCase_WebDriverException as Selenium2TestCase_WebDriverException;

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
     * Returns the first element in the list.
     *
     * @return Selenium2TestCase_Element
     *
     * @throws InvalidArgumentException
     */
    public function element()
    {
        if (empty($this->elements)) {
            throw new InvalidArgumentException('The current element list is empty.');
        }

        return $this->getElement(0);
    }

    /**
     * Returns the attribute value of the first element of the list.
     *
     * @param string $attribute
     *
     * @return string|null
     *
     * @throws InvalidArgumentException
     */
    public function attribute($attribute)
    {
        if (! $this->elements) {
            throw new InvalidArgumentException('The current elements list is empty.');
        }

        return $this->getElement(0)->attribute($attribute);
    }

    /**
     * Returns the element name of the first element of the list.
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    public function elementName()
    {
        if (! $this->elements) {
            throw new InvalidArgumentException('The current element list is empty.');
        }

        return $this->getElement(0)->name();
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
     * Filters the document by the given xpath, css selector, name, id or class attribute.
     *
     * @param mixed $selectors
     *
     * @return Crawler
     */
    public function filter($selectors)
    {
        $elements = [];
        $criteria = [
            'xpath',
            'css selector',
            'name',
            'id',
            'class name'
        ];

        $crawler = $this->createSubCrawler($this->document, null);

        $selectors = explode(',', $selectors);

        foreach ($selectors as $selector) {
            $selector = trim($selector);

            if (empty($elements) && starts_with($selector, ['select', '//select'])) {
                if (! is_null($element = $this->findSelect($selector))) {
                    $elements[] = $element;
                }
            } else {
                foreach ($criteria as $criterion) {
                    if (empty($elements)) {
                        $elements = $this->filterByCriteria($selector, $criterion);
                    }
                }
            }
        }

        $crawler->add($elements);

        return $crawler;
    }

    /**
     * Selects link elements by the given link text.
     *
     * @param string $name
     *
     * @return Crawler
     */
    public function selectLink($name)
    {
        $links = $this->createSubCrawler($this->document, null);

        $links->add($this->filterByCriteria($name, 'link text'));

        return $links;
    }

    /**
     * Selects button elements by the given button text.
     *
     * @param string $name
     *
     * @return Crawler
     */
    public function selectButton($name)
    {
        $buttons = $this->createSubCrawler($this->document, null);

        $buttons->add($this->filterByCriteria("//button[text()[contains(.,'{$name}')]]", 'xpath'));

        return $buttons;
    }

    /**
     * Selects elements by the given element text.
     *
     * @param string $name
     *
     * @return Crawler
     */
    public function selectElement($name)
    {
        $elements = $this->createSubCrawler($this->document, null);

        $elements->add($this->filterByCriteria("//*[text()[contains(.,'{$name}')]]", 'xpath'));

        return $elements;
    }

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
     * Adds the SeleniumTestCase document containing the DOM.
     *
     * @param SeleniumTestCase $document
     */
    private function addDocument(SeleniumTestCase $document)
    {
        $this->document = $document;
    }

    /**
     * Adds an array of Selenium2TestCase Element instances to the list of elements.
     *
     * @param array $elements
     */
    private function addElements(array $elements)
    {
        foreach ($elements as $element) {
            $this->add($element);
        }
    }

    /**
     * Check if the given element exists in the elements array.
     *
     * @param Selenium2TestCase_Element $needle
     *
     * @return bool
     */
    private function hasElement(Selenium2TestCase_Element $needle)
    {
        $elements = array_filter($this->elements, function ($element) use ($needle) {
            return $element->equals($needle);
        });

        return count($elements) > 0;
    }

    /**
     * Adds a Selenium2TestCase Element instance to the list of elements.
     *
     * @param Selenium2TestCase_Element $element
     */
    private function addElement(Selenium2TestCase_Element $element)
    {
        // Don't add duplicate elements in the Crawler
        if ($this->hasElement($element)) {
            return;
        }

        $this->elements[] = $element;
    }

    /**
     * Find a select element by the given xpath, css selector, name or id attribute.
     *
     * @param mixed $selector
     *
     * @return Crawler
     */
    private function findSelect($selector)
    {
        $element = null;
        $criteria = [
            'byXPath',
            'byCssSelector',
            'byName',
            'byId'
        ];

        foreach ($criteria as $criterion) {
            if (is_null($element)) {
                $element = $this->findSelectByCriteria($selector, $criterion);
            }
        }

        return $element;
    }

    /**
     * Filter the elements by the given selector using the given criteria.
     *
     * @param string $selector
     * @param string $criterion
     *
     * @return array
     *
     */
    private function filterByCriteria($selector, $criterion)
    {
        try {
            $elements = $this->document->elements($this->document->using($criterion)->value($selector));
        } catch (Selenium2TestCase_WebDriverException $exception) {
            return [];
        }

        return $elements;
    }

    /**
     * Find the select element by the given selector using the given criteria.
     *
     * @param string $selector
     * @param string $criterion
     *
     * @return null|Selenium2TestCase_Element
     */
    private function findSelectByCriteria($selector, $criterion)
    {
        try {
            return $this->document->select(call_user_func_array([$this->document, $criterion], [$selector]));
        } catch (Selenium2TestCase_WebDriverException $exception) {
            return null;
        }
    }

    /**
     * Creates a crawler for some elements.
     *
     * @param array $elements
     *
     * @return Crawler
     */
    private function createSubCrawler($elements)
    {
        $crawler = new static($this->document, $elements);

        return $crawler;
    }
}
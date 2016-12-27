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
     * Check if the given element exists in the elements array.
     *
     * @param Selenium2TestCase_Element $element
     *
     * @return bool
     */
    public function hasElement(Selenium2TestCase_Element $element)
    {
        $elementId = $element->getId();

        $elements = array_filter($this->elements, function ($element) use ($elementId) {
            return $element->getId() == $elementId;
        });

        return count($elements) > 0;
    }

    /**
     * Adds a Selenium2TestCase Element instance to the list of elements.
     *
     * @param Selenium2TestCase_Element $element
     */
    public function addElement(Selenium2TestCase_Element $element)
    {
        // Don't add duplicate elements in the Crawler
        if ($this->hasElement($element)) {
            return;
        }

        $this->elements[] = $element;
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
     * Returns the selected option values for the first element from the list.
     * Will only return the option values if the first element in the elements list is a 'select' element.
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    public function selectedValues()
    {
        if (empty($this->elements)) {
            throw new InvalidArgumentException('The current element list is empty.');
        }

        return $this->getElement(0)->selectedValues();
    }

    /**
     * Checks iff the first element from the list is selected/checked.
     *
     * @return bool
     *
     * @throws InvalidArgumentException
     */
    public function selected()
    {
        if (empty($this->elements)) {
            throw new InvalidArgumentException('The current element list is empty.');
        }

        return $this->getElement(0)->selected();
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
     * Find a select element by the given xpath, css selector, name or id attribute.
     *
     * @param mixed $selector
     *
     * @return Crawler
     */
    protected function findSelect($selector)
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

        $links->add($this->filterByCriteria($name, 'link text'));

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

//    /**
//     * Filters the document  by the given xpath, css selector, id, name or class attribute.
//     *
//     * @param array $selectors
//     *
//     * @return array
//     */
//    protected function filterElements(array $selectors)
//    {
//        $elements = [];
//        $criteria = [
//            'xpath',
//            'css selector',
//            'name',
//            'id',
//            'class name'
//        ];
//
//        foreach ($selectors as $selector) {
//            $selector = trim($selector);
//
////                if (starts_with($selector, '//')) {
////                    $elements = $this->filterByCriteria($selector, 'xpath');
////                } else {
//            foreach ($criteria as $criterion) {
//                if (empty($elements)) {
////                    try {
//                    $elements = $this->filterByCriteria($selector, $criterion);
////                    } catch (Selenium2TestCase_WebDriverException $e) {
////                        throw new InvalidArgumentException($e->getMessage());
////                        continue;
////                    }
//                }
////                    $elements = $this->filterByCriteria($selector, 'css selector');
////
////                    if (empty($elements)) {
////                        $elements = $this->filterByCriteria($selector, 'name');
////                    }
////
////                    if (empty($elements)) {
////                        $elements = $this->filterByCriteria($selector, 'id');
////                    }
////
////                    if (empty($elements)) {
////                        $elements = $this->filterByCriteria($selector, 'class name');
////                    }
////                }
//            }
//        }
//
//        return $elements;
//    }

    /**
     * Filter the elements by the given selector using the given criteria.
     *
     * @param string $selector
     * @param string $criterion
     *
     * @return array
     *
     */
    protected function filterByCriteria($selector, $criterion)
    {
        try {
            $elements = $this->document->elements($this->document->using($criterion)->value($selector));
        } catch (Selenium2TestCase_WebDriverException $e) {
            return [];
        }

        return $elements;
    }

    /**
     * //     * @param array $selectors
     * //     *
     * //     * @return array
     * //     */
//    protected function findSelect(array $selectors)
//    {
//        $element = null;
//        $criteria = [
//            'byXPath',
//            'byCssSelector',
//            'byName',
//            'byId'
//        ];
//
//        foreach ($selectors as $selector) {
//            $selector = trim($selector);
//
////            if (starts_with($selector, '//')) {
////                $element = $this->findSelectByCriteria($selector, 'byXPath');
////            } else {
//            foreach ($criteria as $criterion) {
//                if (is_null($element)) {
////                        try {
//                    $element = $this->findSelectByCriteria($selector, $criterion);
////                        } catch (Selenium2TestCase_WebDriverException $e) {
////                            continue;
////                        }
//                }
//            }
////                $element = $this->findSelectByCriteria($selector, 'byCssSelector');
////
////                if (is_null($element)) {
////                    $element = $this->findSelectByCriteria($selector, 'byName');
////                }
////
////                if (is_null($element)) {
////                    $element = $this->findSelectByCriteria($selector, 'byId');
////                }
////            }
//        }
//
//        return $element;
//    }

    /**
     * Find the select element by the given selector using the given criteria.
     *
     * @param string $selector
     * @param string $criterion
     *
     * @return null|Selenium2TestCase_Element
     */
    protected
    function findSelectByCriteria(
        $selector,
        $criterion
    ) {
        try {
//            return $this->document->select($this->document->byXPath($selector));
            return $this->document->select(call_user_func_array([$this->document, $criterion], [$selector]));
        } catch (Selenium2TestCase_WebDriverException $exception) {
            return null;
        }
    }

//    /**
//     * Filter the elements by css selector.
//     *
//     * @param string $selector
//     *
//     * @return array
//     */
//    protected function filterByCss($selector)
//    {
//        try {
//            $elements = $this->isSelect
//                ? $this->document->select($this->document->byCssSelector($selector))
//                : $this->document->elements($this->document->using('css selector')->value($selector));
//        } catch (Selenium2TestCase_WebDriverException $exception) {
//            return [];
//        }
//
//        return $elements;
//    }

//    /**
//     * Filter the elements by name attribute.
//     *
//     * @param string $selector
//     *
//     * @return array
//     */
//    protected function filterByName($selector)
//    {
//        try {
//            $elements = $this->isSelect
//                ? $this->document->select($this->document->byName($selector))
//                : $this->document->elements($this->document->using('name')->value($selector));
//        } catch (Selenium2TestCase_WebDriverException $exception) {
//            return [];
//        }
//
//        return $elements;
//    }

//    /**
//     * Filter the elements by id attribute.
//     *
//     * @param string $selector
//     *
//     * @return array
//     */
//    protected function filterById($selector)
//    {
//        if (starts_with($selector, '.')) {
//            return [];
//        }
//
//        $elements = $this->document->elements($this->document->using('id')->value($selector));
//
//        return $elements;
//    }

//    /**
//     * Filter the elements by class attribute.
//     *
//     * @param string $selector
//     *
//     * @return array
//     */
//    protected function filterByClass($selector)
//    {
//        // Using 'class name' with a selector that starts with '#' causes an error to be thrown.
//        // Return an empty array, as the '#' is explicitly stating to filter the document by id attribute
//        // and this is the filter for class attributes.
//        if (starts_with($selector, '#')) {
//            return [];
//        }
//
//        $elements = $this->document->elements($this->document->using('class name')->value($selector));
//
//        return $elements;
//    }

//    /**
//     * Filter the elements by link text.
//     *
//     * @param string $text
//     *
//     * @return array
//     */
//    protected function filterByLinkText($text)
//    {
//        $elements = $this->document->elements($this->document->using('link text')->value($text));
//
//        return $elements;
//    }

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
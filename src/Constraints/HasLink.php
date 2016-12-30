<?php

namespace SeleniumTesting\Constraints;

class HasLink extends PageConstraint
{

    /**
     * The text expected to be found.
     *
     * @var string
     */
    protected $text;

    /**
     * The URL expected to be linked in the <a> tag.
     *
     * @var string|null
     */
    protected $url;

    /**
     * Create a new constraint instance.
     *
     * @param  string      $text
     * @param  string|null $url
     */
    public function __construct($text, $url = null)
    {
        $this->url = $url;
        $this->text = $text;
    }

    /**
     * Check if the link is found in the given crawler.
     *
     * @param  \SeleniumTesting\Crawler|string $crawler
     *
     * @return bool
     */
    protected function matches($crawler)
    {
        $links = $this->crawler($crawler)->selectLink($this->text);

        if ($links->count() == 0) {
            return false;
        }

        if (is_null($this->url)) {
            return true;
        }

        $absoluteUrl = $this->absoluteUrl();

        foreach ($links as $link) {
            $linkHref = rtrim($link->attribute('href'), '/');

            if ($linkHref == $this->url || str_contains($linkHref, $absoluteUrl)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the description of the failure.
     *
     * @return string
     */
    protected function getFailureDescription()
    {
        $description = "the page has a link with the text [{$this->text}]";

        if ($this->url) {
            $description .= " and the URL [{$this->url}]";
        }

        return $description;
    }

    /**
     * Returns the reversed description of the failure.
     *
     * @return string
     */
    protected function getReverseFailureDescription()
    {
        $description = "the page does not have a link with the text [{$this->text}]";

        if ($this->url) {
            $description .= " and the URL [{$this->url}]";
        }

        return $description;
    }

    /**
     * Add a root if the URL is relative and strip any trailing slashes.
     *
     * @return mixed
     */
    private function absoluteUrl()
    {
        $url = rtrim($this->url, '/');

        if (! starts_with($url, ['http', 'https'])) {
            return ['http://'.$url, 'https://'.$url];
        }

        return $url;
    }
}

<?php

namespace PODEntender\SitemapGenerator;

class UrlSet implements \IteratorAggregate
{
    /** @var Url[] */
    private $urls = [];

    public function __construct(array $urls)
    {
        // @todo -> assert elements are of type \PODEntender\SitemapGenerator\Domain\Model\Sitemap\Url
        $this->urls = $urls;
    }

    public function getIterator(): \Iterator
    {
        return new \ArrayIterator($this->urls);
    }
}

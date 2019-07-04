<?php

namespace PODEntender\SitemapGenerator\Adapter\Jigsaw;

use PODEntender\SitemapGenerator\Factory\SitemapXmlFactory;
use PODEntender\SitemapGenerator\Factory\UrlSetFactory;

use Illuminate\Support\Collection;
use TightenCo\Jigsaw\PageVariable;
use DOMDocument;

class JigsawAdapter
{
    private $sitemapXmlFactory;

    private $urlSetFactory;

    public function __construct(SitemapXmlFactory $sitemapXmlFactory, UrlSetFactory $urlSetFactory)
    {
        $this->sitemapXmlFactory = $sitemapXmlFactory;
        $this->urlSetFactory = $urlSetFactory;
    }

    public function fromCollection(Collection $pages): DOMDocument
    {
        $multiDimensionalArray = $pages
            ->map(function (PageVariable $page) {
                $date = $this->fetchDataFromPage('lastModified', $page);

                // Fallback to default post date metadata
                if ($date === null && $page->date) {
                    $date = date_create_immutable(date('Y-m-d', $page->date));
                }

                return [
                    'location' => $this->fetchDataFromPage('location', $page) ?? $page->getUrl(),
                    'lastModified' => $date,
                    'changeFrequency' => $this->fetchDataFromPage('changeFrequency', $page),
                    'priority' => $this->fetchDataFromPage('priority', $page),
                ];
            })
            ->toArray();

        return $this->sitemapXmlFactory->createFromUrlSet(
            $this->urlSetFactory->createFromMultiDimensionalArray($multiDimensionalArray)
        );
    }

    private function fetchDataFromPage(string $identifier, PageVariable $page)
    {
        // Search for sitemap item in page property/config
        if ($page->sitemap === null || isset($page->sitemap[$identifier]) === false) {
            return null;
        }

        if (is_callable($page->sitemap[$identifier])) {
            return call_user_func($page->sitemap[$identifier], $page);
        }

        return (string) $page->sitemap[$identifier];
    }
}

<?php

namespace PODEntender\SitemapGenerator\Adapter\Jigsaw;

use PODEntender\SitemapGenerator\Factory\SitemapXmlFactory;
use PODEntender\SitemapGenerator\Factory\UrlSetFactory;
use TightenCo\Jigsaw\Collection\Collection;
use DOMDocument;
use TightenCo\Jigsaw\PageVariable;

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
                $output = [
                    'location' => $page->getUrl(),
                ];

                if ($page->date) {
                    $output['lastModified'] = date_create_immutable(date('Y-m-d', $page->date));
                }

                if ($page->get('sitemap') && isset($page->sitemap['changeFrequency'])) {
                    $output['changeFrequency'] = $page->sitemap['changeFrequency'];
                }

                if ($page->get('sitemap') && isset($page->sitemap['priority'])) {
                    $output['priority'] = $page->sitemap['priority'];
                }

                return $output;
            })
            ->toArray();

        return $this->sitemapXmlFactory->createFromUrlSet(
            $this->urlSetFactory->createFromMultiDimensionalArray($multiDimensionalArray)
        );
    }
}

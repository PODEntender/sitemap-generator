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
                $sitemapUrl = $page->getUrl();

                $parsedUrl = parse_url($sitemapUrl);

                // If index page, don't append trailing forward slash
                if (false === isset($parsedUrl['path'])) {
                    $sitemapUrl = rtrim($sitemapUrl, '/');
                } else {
                    $pathInfo = pathinfo($parsedUrl['path']);

                    // If .html, send it right away. If path-like, make sure last character is a forward slash
                    $sitemapUrl = isset($pathInfo['extension']) ? $sitemapUrl : rtrim($sitemapUrl, '/') . '/';
                }

                $output = ['location' => $sitemapUrl];

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

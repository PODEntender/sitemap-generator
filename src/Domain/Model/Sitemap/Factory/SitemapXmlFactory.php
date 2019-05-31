<?php

namespace PODEntender\SitemapGenerator\Domain\Model\Sitemap\Factory;

use PODEntender\SitemapGenerator\Domain\Model\Sitemap\UrlSet;

use \DOMDocument;

class SitemapXmlFactory
{
    const DOM_VERSION = '1.0';

    const DOM_ENCODING = 'utf-8';

    const DATE_FORMAT = 'Y-m-d';

    public function createFromUrlSet(UrlSet $urlSet): DOMDocument
    {
        $urlIterator = $urlSet->getIterator();
        $document = new DOMDocument(self::DOM_VERSION, self::DOM_ENCODING);
        $root = $document->createElement('urlset');
        $root->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        foreach ($urlIterator as $url) {
            $urlNode = $document->createElement('url');
            $urlNode->appendChild($document->createElement('loc', $url->location()));

            if ($url->lastModified() !== null) {
                $urlNode->appendChild(
                    $document->createElement('lastmod', $url->lastModified()->format(self::DATE_FORMAT))
                );
            }

            if ($url->changeFrequency() !== null) {
                $urlNode->appendChild($document->createElement('changefreq', $url->changeFrequency()));
            }

            if ($url->priority() !== null) {
                $urlNode->appendChild($document->createElement('priority', $url->priority()));
            }

            $root->appendChild($urlNode);
        }

        $document->appendChild($root);
        return $document;
    }
}

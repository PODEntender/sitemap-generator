<?php

namespace PODEntender\SitemapGenerator\Factory;

use PHPUnit\Framework\TestCase;
use PODEntender\SitemapGenerator\Url;
use PODEntender\SitemapGenerator\UrlSet;

use \DateTime;

class SitemapXmlFactoryTest extends TestCase
{
    private $sitemapXmlFactory;

    private $urlSetFactory;

    protected function setUp(): void
    {
        $this->sitemapXmlFactory = new SitemapXmlFactory();
        $this->urlSetFactory = new UrlSetFactory();
    }

    public function testCreateFromUrlSetCreateUrlSetRootElement(): void
    {
        $dom = $this->sitemapXmlFactory->createFromUrlSet(new UrlSet([]));

        $this->assertEquals('1.0', $dom->xmlVersion);
        $this->assertEquals('utf-8', $dom->xmlEncoding);

        $root = $dom->getElementsByTagName('urlset')->item(0);
        $this->assertEquals('urlset', $root->nodeName);
        $this->assertEquals($root->getAttribute('xmlns'), 'http://www.sitemaps.org/schemas/sitemap/0.9');
    }

    public function testCreateFromUrlSetCreatesUrlElements(): void
    {
        $urlSet = $this->urlSetFactory->createFromUrlArray([
            'https://podentender.com/first-url/',
            'https://podentender.com/second-url/',
        ]);
        $root = $this->sitemapXmlFactory->createFromUrlSet($urlSet)->firstChild;

        $this->assertEquals(2, $root->childNodes->count());

        $firstUrl = $root->childNodes->item(0);
        $this->assertEquals(
            'https://podentender.com/first-url/',
            $firstUrl->getElementsByTagName('loc')->item(0)->textContent
        );

        $secondUrl = $root->childNodes->item(1);
        $this->assertEquals(
            'https://podentender.com/second-url/',
            $secondUrl->getElementsByTagName('loc')->item(0)->textContent
        );
    }

    public function testCreateFromUrlSetCreatesPartialAndCompleteUrlElements(): void
    {
        $urlSet = $this->urlSetFactory->createFromMultiDimensionalArray([
            [
                'location' => 'https://podentender.com/first-url/',
                'changeFrequency' => Url::FREQUENCY_MONTHLY,
            ],
            [
                'location' => 'https://podentender.com/second-url/',
                'lastModified' => new DateTime('2019-05-01'),
                'priority' => 0.8,
            ],
        ]);
        $root = $this->sitemapXmlFactory->createFromUrlSet($urlSet)->firstChild;

        $this->assertEquals(2, $root->childNodes->count());

        $firstUrl = $root->childNodes->item(0);
        $this->assertEquals(
            'https://podentender.com/first-url/',
            $firstUrl->getElementsByTagName('loc')->item(0)->textContent
        );
        $this->assertEquals(
            'monthly',
            $firstUrl->getElementsByTagName('changefreq')->item(0)->textContent
        );

        $secondUrl = $root->childNodes->item(1);
        $this->assertEquals(
            'https://podentender.com/second-url/',
            $secondUrl->getElementsByTagName('loc')->item(0)->textContent
        );
        $this->assertEquals(
            '2019-05-01',
            $secondUrl->getElementsByTagName('lastmod')->item(0)->textContent
        );
        $this->assertEquals(
            0.8,
            $secondUrl->getElementsByTagName('priority')->item(0)->textContent
        );
    }
}

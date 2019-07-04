<?php

namespace PODEntender\SitemapGenerator\Adapter\Jigsaw;

use PHPUnit\Framework\TestCase;
use PODEntender\SitemapGenerator\Factory\SitemapXmlFactory;
use PODEntender\SitemapGenerator\Factory\UrlSetFactory;
use PODEntender\SitemapGenerator\Url;
use TightenCo\Jigsaw\Collection\Collection;
use TightenCo\Jigsaw\IterableObject;
use TightenCo\Jigsaw\Jigsaw;
use TightenCo\Jigsaw\PageVariable;

class JigsawAdapterTest extends TestCase
{
    private $jigsawAdapter;

    protected function setUp(): void
    {
        if (false === class_exists(Jigsaw::class)) {
            $this->markTestSkipped('Package "tightenco/jigsaw" is not present, skipping test.');

            return;
        }

        $this->jigsawAdapter = new JigsawAdapter(
            new SitemapXmlFactory(),
            new UrlSetFactory()
        );
    }

    public function testFromCollectionBackwardsCompatibility(): void
    {
        $collection = new Collection([
            $this->createJigsawPage('page/1.html'),
            $this->createJigsawPage('page/2.html', '2019-01-01'),
            $this->createJigsawPage('page/3', '2019-01-02', Url::FREQUENCY_NEVER),
            $this->createJigsawPage('page/4/', '2019-01-03', null, 0.7),
        ]);

        $sitemap = $this->jigsawAdapter->fromCollection($collection);

        $expectedOutput = '<?xml version="1.0" encoding="utf-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"><url><loc>https://podentender.com/page/1.html</loc></url><url><loc>https://podentender.com/page/2.html</loc><lastmod>2019-01-01</lastmod></url><url><loc>https://podentender.com/page/3</loc><lastmod>2019-01-02</lastmod><changefreq>never</changefreq></url><url><loc>https://podentender.com/page/4/</loc><lastmod>2019-01-03</lastmod><priority>0.7</priority></url></urlset>';

        $this->assertEquals($expectedOutput, str_replace(PHP_EOL, '', $sitemap->saveXML()));
    }

    public function testFromCollectionUsesSitemapMetadataSection(): void
    {
        $collection = new Collection([
            $this->createJigsawPage('page/1.html', '2019-01-01', 'monthly', 0.6, '/forcedUrl.html'),
        ]);

        $sitemap = $this->jigsawAdapter->fromCollection($collection);
        $expectedOutput = '<?xml version="1.0" encoding="utf-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"><url><loc>/forcedUrl.html</loc><lastmod>2019-01-01</lastmod><changefreq>monthly</changefreq><priority>0.6</priority></url></urlset>';

        $this->assertEquals($expectedOutput, str_replace(PHP_EOL, '', $sitemap->saveXML()));
    }

    public function testFromCollectionUsesSitemapMetadataCallbacks(): void
    {
        $dateCallback = function (PageVariable $page) {
            return new \DateTimeImmutable('2019-01-01');
        };
        $frequencyCallback = function (PageVariable $page) {
            return 'monthly';
        };
        $priorityCallback = function (PageVariable $page) {
            return 0.6;
        };
        $urlCallback = function (PageVariable $page) {
            return '/forcedUrl.html';
        };

        $collection = new Collection([
            $this->createJigsawPage('page/1.html', $dateCallback, $frequencyCallback, $priorityCallback, $urlCallback),
        ]);

        $sitemap = $this->jigsawAdapter->fromCollection($collection);
        $expectedOutput = '<?xml version="1.0" encoding="utf-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"><url><loc>/forcedUrl.html</loc><lastmod>2019-01-01</lastmod><changefreq>monthly</changefreq><priority>0.6</priority></url></urlset>';

        $this->assertEquals($expectedOutput, str_replace(PHP_EOL, '', $sitemap->saveXML()));
    }

    private function createJigsawPage(
        $url,
        $date = null,
        $changeFrequency = null,
        $priority = null,
        $forceUrl = null
    ): PageVariable
    {
        $page = [
            'extends' => '_layouts/test-base',
            'date' => is_callable($date) ? null : strtotime($date),
            '_meta' => new IterableObject([
                'url' => 'https://podentender.com/' . $url,
            ]),
        ];

        $page['sitemap'] = [];

        if (is_callable($date)) {
            $page['sitemap']['lastModified'] = $date;
        }

        if ($changeFrequency) {
            $page['sitemap']['changeFrequency'] = $changeFrequency;
        }

        if ($priority) {
            $page['sitemap']['priority'] = $priority;
        }

        if ($forceUrl) {
            $page['sitemap']['location'] = $forceUrl;
        }

        return new PageVariable($page);
    }
}

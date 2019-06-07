<?php

namespace PODEntender\SitemapGenerator\Url\Factory;

use PHPUnit\Framework\TestCase;
use PODEntender\SitemapGenerator\Factory\UrlSetFactory;
use PODEntender\SitemapGenerator\Url;

class UrlSetFactoryTest extends TestCase
{
    /** @var UrlSetFactory */
    private $factory;

    protected function setUp(): void
    {
        $this->factory = new UrlSetFactory();
    }

    public function testCreateFromArray(): void
    {
        $urls = [
            'https://podentender.com/first-url/',
            'https://podentender.com/second-url/',
            'https://podentender.com/third-url/',
        ];
        $urlIterator = $this->factory->createFromUrlArray($urls)->getIterator();

        while ($urlIterator->valid()) {
            $position = $urlIterator->key();

            /** @var Url $url */
            $url = $urlIterator->current();
            $this->assertEquals($urls[$position], $url->location());

            $this->assertNull($url->lastModified());
            $this->assertNull($url->changeFrequency());
            $this->assertNull($url->priority());

            $urlIterator->next();
        }
    }

    public function testCreateFromMultiDimensionalArray(): void
    {
        $urls = [
            [
                'location' => 'https://podentender.com/first-url/',
                'lastModified' => new \DateTimeImmutable('2019-01-01'),
            ],
            [
                'location' => 'https://podentender.com/second-url/',
                'lastModified' => new \DateTimeImmutable('2019-02-01'),
            ],
        ];
        $urlIterator = $this->factory->createFromMultiDimensionalArray($urls)->getIterator();

        while ($urlIterator->valid()) {
            $position = $urlIterator->key();

            /** @var Url $url */
            $url = $urlIterator->current();
            $this->assertEquals($urls[$position]['location'], $url->location());
            $this->assertEquals($urls[$position]['lastModified'], $url->lastModified());

            $this->assertNull($url->changeFrequency());
            $this->assertNull($url->priority());

            $urlIterator->next();
        }
    }
}

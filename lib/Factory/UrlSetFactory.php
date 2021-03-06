<?php

namespace PODEntender\SitemapGenerator\Factory;

use PODEntender\SitemapGenerator\Url;
use PODEntender\SitemapGenerator\UrlSet;

class UrlSetFactory
{
    public function createFromUrlArray(array $urls): UrlSet
    {
        return new UrlSet(array_map(function (string $location) {
            return new Url($location, null, null, null);
        }, $urls));
    }

    public function createFromMultiDimensionalArray(array $urls): UrlSet
    {
        return new UrlSet(array_map(function (array $urlDefinition) {
            return new Url(
                $urlDefinition['location'],
                $urlDefinition['lastModified'] ?? null,
                $urlDefinition['changeFrequency'] ?? null,
                $urlDefinition['priority'] ?? null
            );
        }, $urls));
    }
}

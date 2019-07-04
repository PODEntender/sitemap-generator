# How to use the \PODEntender\SitemapGenerator\Jigsaw\Adapter\JigsawAdapter

In order to use this adapter there are **two main things** you must pay attention to:

- The required metadata
- The event handler configuration

## Setting sitemap metadata

As defined in [sitemaps.org](https://www.sitemaps.org), a sitemap will contain one required and three optional fields
for each url:

- **(required)** `<loc>`: the actual URL to be set on the sitemap
- **(optional)** `<changefreq>` a string representing how often this url receives updates
- **(optional)** `<lastmod>` a string representing the last time this url was updated
- **(optional)** `<priority>` a float from `0` to `1` representing the weight of this url

The tags are mapped to full names under configs for Jigsaw. See the table bellow to check which properties you can set:

Sitemap Tag    | Jigsaw Property | Type
-------------- | --------------- | ------
`<loc>`        | location        | **string**
`<lastmod>`    | lastModified    | **\DateTimeInterface** (nullable)
`<changefreq>` | changeFrequency | **string** (nullable)
`<priority>`   | priority        | **float** (nullable)

**There are two ways to set** such metadata and they are mutually exclusive. Options are **by global configuration** or
**by file metadata**. Where global configuration takes place if no metadata by file is set.

**Important**: if you set sitemap metadata on the `md` file itself, it will overwrite ALL sitemap metadata for this
page, meaning that global metadata won't apply.

### By global configuration

On your `config.php` file you are able to define collections. Besides defining collections, you can also set custom
metadata and functions that Jigsaw will pass down to your views. `JigsawAdapter` will attempt to read items under the
`sitemap` section of your collection's metadata.

Here's an example:

```php
// config.php
<?php
return [
    'collections' => [
        'posts' => [
            // everything you'd normally have in your collection configuration
            'sitemap' => [
                // Metadata can receive callback functions
                'location' => function (\TightenCo\Jigsaw\PageVariable $page) {
                    return $page->getUrl();
                },
                // Metadata can also receive static values
                'changeFrequency' => 'monthly',
            ],
        ],
    ],
];
```

### By file metadata

On your `.md` or `.blade.md` files under `source/` you can also set metadata as you're already used to. Just add a
sitemap section to it and set values as you desire.

Here's another example:

```markdown
--- // post[.blade].md
extends: _layouts.posts
...

sitemap:
  lastModified: 2019-06-01
  changeFrequency: daily
  priority: 1
---
```

**Notice** that by setting metadata here, no global metadata won't apply to this page.

Let's say you have globally set `changeFrequency` equals to `weekly`, and on your specific file you decided to set only
`priority` to `0.8`. The output won't contain the global metadata "changefreq".

## Connecting JigsawAdapter to your Jigsaw project

The most common use case for this adapter is hooking it into the `afterBuild` event.

In your `bootstrap.php` file add the following lines:

```php
use \PODEntender\SitemapGenerator\Jigsaw\Adapter\JigsawAdapter;

$events->afterBuild(function (Jigsaw $jigsaw) {
    $pages; // Make your logic to fetch as many pages as you wish
    
    // Fetches the adapter from Dependency Injection
    $sitemapGenerator = $jigsaw->app->get(JigsawAdapter::class);
    
    // Fetches the DOMDocument instance from $pages
    $domDocument = $sitemapGenerator->fromCollection($pages);
    
    // Transforms into a XML string
    $outputXml = $domDocument->saveXML();
    
    // Do whatever you want with that string
    ...
});
```

## Example: posts and people collections

Let's say you two collections you want to present in your sitemap. They're named `posts` and `people`.
In order to publish all pages from these collections, do the following:

### Option 1: Generating a single Sitemap for all collections

```php
use \PODEntender\SitemapGenerator\Jigsaw\Adapter\JigsawAdapter;

$events->afterBuild(function (Jigsaw $jigsaw) {
    $posts = $jigsaw->getCollection('posts');
    $people = $jigsaw->getCollection('people');
    
    // Merging all pages into a single collection
    $pages = $posts->merge($people);
    
    // Fetches the adapter from Dependency Injection
    $sitemapGenerator = $jigsaw->app->get(JigsawAdapter::class);
    
    // Fetches the DOMDocument instance from $pages
    $domDocument = $sitemapGenerator->fromCollection($pages);
    
    // Transforms into a XML string
    $outputXml = $domDocument->saveXML();
    
    // Save xml into disk
    $destinationPath = $jigsaw->getDestinationPath() . '/sitemap.xml'; // build_local/sitemap.xml
    file_put_contents($destinationPath, $outputXml);
});
```

### Option 2: Generating multiple Sitemaps from different collections

```php
use \PODEntender\SitemapGenerator\Jigsaw\Adapter\JigsawAdapter;

$events->afterBuild(function (Jigsaw $jigsaw) {
    $outputPath = $jigsaw->getDestinationPath();
    
    // Fetches the adapter from Dependency Injection
    $sitemapGenerator = $jigsaw->app->get(JigsawAdapter::class);
    
    // Saves posts sitemap into build_local/posts-sitemap.xml
    $posts = $jigsaw->getCollection('posts');
    $postsSitemap = $sitemapGenerator->fromCollection($posts);
    file_put_contents($outputPath . '/posts-sitemap.xml', $postsSitemap->saveXML());
    
    // Saves people sitemap into build_local/people-sitemap.xml
    $people = $jigsaw->getCollection('people');
    $peopleSitemap = $sitemapGenerator->fromCollection($people);
    file_put_contents($outputPath . '/people-sitemap.xml', $peopleSitemap->saveXML());
});
```

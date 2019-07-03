# How to use the \PODEntender\SitemapGenerator\Jigsaw\Adapter\JigsawAdapter

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

# Clumsy Sitemap
Simple sitemaps for Laravel projects

[![Latest Stable Version](https://poser.pugx.org/clumsy/sitemap/version)](https://packagist.org/packages/clumsy/sitemap) [![Latest Unstable Version](https://poser.pugx.org/clumsy/sitemap/v/unstable)](//packagist.org/packages/clumsy/sitemap) [![Codacy Badge](https://api.codacy.com/project/badge/Grade/35aaffa60b424bedab0dda7d825ca43e)](https://www.codacy.com/app/tbuteler/clumsy-sitemap?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=tbuteler/clumsy-sitemap&amp;utm_campaign=Badge_Grade) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/c7722d5c-37e7-490b-88c8-bc04fd77434e/mini.png)](https://insight.sensiolabs.com/projects/c7722d5c-37e7-490b-88c8-bc04fd77434e)

## Installing

- Use Composer to install:
```
composer require clumsy/sitemap
```

- In the `config/app.php` file, add this to the `providers` key:
```php
Clumsy\Sitemap\SitemapServiceProvider::class,
```

## Usage

The package automatically creates a route to resolve http://example.com/sitemap.xml for you. If there are no URLs to insert on your sitemap.xml or an error occurs while parsing them, a `404` error will be thrown.

In order to add URLs to your sitemap, add a `sitemap.php` file inside the `app/Http` folder of your Laravel app. Inside, return an array with the desired URLs. For example:

```php
<?php

return [
    url('/')
];
```

Will yield the following entry in your sitemap.xml:
```
...
<url>
    <loc>http://workbench.local</loc>
</url>
...
```

To add tags to the URLs, make the array associative, using the `links` key as your collection of URLs:

```php
<?php

return [
    'changefreq' => 'monthly',
    'priority' => '0.8',
    'lastmod' => '2016-08-04',
    'links' => [
        url('/'),
    ]
];
```

If you want different URLs to have different values for the supporting tags, use more than one array:

```php
<?php

return [
    [
        'changefreq' => 'daily',
        'priority' => '1.0',
        'links' => [
            App\Models\Resource::where('active', true)->get()->pluck('permalink'),
        ],
    ],
    [
        'changefreq' => 'monthly',
        'priority' => '0.8',
        'lastmod' => '2016-08-04',
        'links' => [
            url('/'),
        ],
    ],
];
```


## Customizing

You can optionally edit the path of the `sitemap.php` file which will contain your URLs and attach middleware to the sitemap route by publishing the default config to your local app:
```
php artisan php artisan vendor:publish --provider="Clumsy\Sitemap\SitemapServiceProvider" --tag=config
```

## Legacy

For Laravel 4.1 or 4.2 projects, use the `0.1` branch.

## Learn more
Visit [sitemaps.org](http://www.sitemaps.org/protocol.html) for more info on the protocol.

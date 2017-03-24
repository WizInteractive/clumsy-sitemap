<?php

namespace Wizclumsy\Sitemap;

class Sitemap
{
    protected $xml;

    protected $schemas = [
        'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"',
        'xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"',
        'xmlns:video="http://www.google.com/schemas/sitemap-video/1.1"',
    ];

    protected $links = [];

    protected function wrap($string, $tag)
    {
        return "<{$tag}>{$string}</{$tag}>";
    }

    public function addSchema($schema)
    {
        $this->schemas[] = $schema;
    }

    public function addLink($link, $lastmod = null, $priority = null, $changefreq = null)
    {
        $this->links[] = compact('link', 'lastmod', 'priority', 'changefreq');
    }

    public function renderLink(array $link)
    {
        $xml = $this->wrap(array_get($link, 'link'), 'loc');

        $optional = [
            'lastmod',
            'changefreq',
            'priority',
        ];

        foreach ($optional as $key) {
            if (array_get($link, $key)) {
                $xml .= $this->wrap($link[$key], $key);
            }
        }

        return $this->wrap($xml, 'url');
    }

    public function render()
    {
        $this->xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $this->xml .= '<urlset '.implode(' ', $this->schemas).'>';

        foreach ($this->links as $link) {
            $this->xml .= $this->renderLink($link);
        }

        $this->xml .= '</urlset>';

        return $this->xml;
    }

    public function __toString()
    {
        return $this->render();
    }
}

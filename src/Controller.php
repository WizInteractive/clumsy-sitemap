<?php

namespace Clumsy\Sitemap;

use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    protected $sitemap;

    protected $groups;

    public function __construct()
    {
        $this->sitemap = new Sitemap;
    }

    protected function missing()
    {
        return abort(404);
    }

    protected function addLink($link, $lastmod = null, $priority = null, $changefreq = null)
    {
        $this->sitemap->addLink($link, $lastmod, $priority, $changefreq);
    }

    protected function addGroup(array $group)
    {
        // If array is not associative and has a link key, then use the whole group as links array
        $links = array_get($group, 'links', $group);

        $lastmod = array_get($group, 'lastmod');
        $priority = array_get($group, 'priority');
        $changefreq = array_get($group, 'changefreq');

        foreach ($links as $link) {
            $this->addLink($link, $lastmod, $priority, $changefreq);
        }
    }

    protected function parseGroups()
    {
        if (array_get($this->groups, 'links')) {
            return $this->addGroup($this->groups);
        }

        foreach ($this->groups as $group) {

            if (is_array($group)) {
                $this->addGroup($group);
                continue;
            }

            $this->addLink($group);
        }
    }

    public function render()
    {
        $path = app_path(config('clumsy.sitemap.path'));

        try {

            $this->groups = require $path;

        } catch (\Exception $e) {

            return $this->missing();
        }

        if (!is_array($this->groups) || !count($this->groups)) {
            return $this->missing();
        }

        $this->parseGroups();

        return response($this->sitemap)->header('Content-Type', 'application/xml');
    }
}

<?php

namespace Clumsy\Sitemap;

use ArrayAccess;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;

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
        return App::abort(404);
    }

    protected function isGroup($var)
    {
        return is_array($var) || $var instanceof ArrayAccess;
    }

    protected function addLink($link, $lastmod = null, $priority = null, $changefreq = null)
    {
        if ($this->isGroup($link)) {

            foreach ($link as $l) {
                $this->addLink($l, $lastmod, $priority, $changefreq);
            }

        } else {

            $this->sitemap->addLink($link, $lastmod, $priority, $changefreq);
        }
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

            if ($this->isGroup($group)) {
                $this->addGroup($group);
                continue;
            }

            $this->addLink($group);
        }
    }

    public function render()
    {
        $path = app_path(Config::get('clumsy/sitemap::config.path'));

        try {

            $this->groups = include $path;

        } catch (\Exception $e) {

            return $this->missing();
        }

        if (!$this->isGroup($this->groups) || !count($this->groups)) {
            return $this->missing();
        }

        $this->parseGroups();

        return Response::make($this->sitemap)->header('Content-Type', 'application/xml');
    }
}

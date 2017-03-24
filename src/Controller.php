<?php

namespace Wizclumsy\Sitemap;

use ArrayAccess;
use Illuminate\Foundation\Application;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    protected $sitemap;

    protected $app;

    protected $groups;

    public function __construct(Application $app)
    {
        $this->sitemap = new Sitemap;
        $this->app = $app;
    }

    protected function missing()
    {
        return abort(404);
    }

    protected function isGroup($var)
    {
        return is_array($var) || $var instanceof ArrayAccess;
    }

    protected function addLink($link, $lastmod = null, $priority = null, $changefreq = null)
    {
        if (!$this->isGroup($link)) {
            return $this->sitemap->addLink($link, $lastmod, $priority, $changefreq);
        }

        foreach ($link as $l) {
            $this->addLink($l, $lastmod, $priority, $changefreq);
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
        $path = base_path(config('clumsy.sitemap.path'));

        try {

            $this->groups = include $path;

        } catch (\Exception $e) {
            // Log the exception before returning a 404
            $this->app->log->error($e);
            return $this->missing();
        }

        if (!$this->isGroup($this->groups) || !count($this->groups)) {
            return $this->missing();
        }

        $this->parseGroups();

        return response($this->sitemap)->header('Content-Type', 'application/xml');
    }
}

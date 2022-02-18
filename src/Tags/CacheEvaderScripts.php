<?php

namespace Alps\CacheEvader\Tags;

use Statamic\Tags\Tags;

class CacheEvaderScripts extends Tags
{
    /**
     * The {{ cache_evader_scripts }} tag.
     */
    public function index()
    {
        $scriptSource = '/vendor/statamic-cache-evader/js/app.js';

        return "<script src=\"${scriptSource}\" defer async></script>";
    }
}

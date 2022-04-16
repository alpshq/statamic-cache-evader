<?php

namespace Alps\CacheEvader\Tags;

use Illuminate\Support\Facades\URL;
use Statamic\Tags\Tags;

class CacheEvaderPartial extends Tags
{
    /**
     * The {{ cache_evader_partial }} tag.
     */
    public function index()
    {
        $wrap = $this->params->get('wrap', 'div');
        $src = $this->params->get('src');

        $other = $this->params->except(['src', 'wrap']);

        $params = array_merge([
            'view' => $src,
        ], $other->all());

        $url = URL::signedRoute('cache-evader.render', $params, null, false);

        $slot = $this->isPair ? trim($this->parse()) : null;

        return '<' . $wrap . ' class="cache-evader-inject" data-url="' . rawurlencode($url) . '">' . $slot . '</' . $wrap . '>';
    }

    /**
     * The {{ cache_evader_partial:* }} tag.
     */
    public function wildcard(string $template)
    {
        $params = array_merge($this->params->all(), [
            'src' => $template,
        ]);

        $this->setParameters($params);

        return $this->index();
    }
}

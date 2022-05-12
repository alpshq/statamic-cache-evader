<?php

namespace Alps\CacheEvader\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Statamic\View\View;

class RenderController extends Controller
{
    public function __invoke(Request $request)
    {
        if (!$request->hasValidSignature(false)) {
            abort(401);
        }

        $viewName = $request->input('view');

        if (!$viewName) {
            abort(404);
        }

        $partial = str_replace('/', '.', $viewName);
        $underscored = $this->underscoredViewName($partial);

        $possibleViews = [
            $underscored,
            'partials.' . $partial,
            'partials.' . $underscored,
        ];

        return View::first($possibleViews, $request->except(['view', 'signature']))
            ->render();
    }

    private function underscoredViewName($partial)
    {
        $bits = collect(explode('.', $partial));

        $last = $bits->pull($bits->count() - 1);

        return $bits->implode('.').'._'.$last;
    }
}

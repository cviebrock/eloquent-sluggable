<?php namespace Cviebrock\EloquentSluggable\Middleware;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\RouteUrlGenerator;
use Illuminate\Support\Facades\DB;

class RedirectOnOldSlug
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     * @throws \Illuminate\Routing\Exceptions\UrlGenerationException
     */
    public function handle(Request $request, \Closure $next)
    {
        $route = $request->route();
        if ($oldSlug = $this->findOldSlug($route->parameter('slug'))) {
            $currentSlug = $this->findSlugFrom($oldSlug);
            $path = $this->routeGenerator($request)->to(
                $request->route(),
                array_merge($route->parameters(), ['slug' => $currentSlug])
            );

            return new RedirectResponse($path);
        }

        return $next($request);
    }

    private function findOldSlug($slug)
    {
        return DB::table('old_slugs')->where('slug', $slug)->first();
    }

    private function findSlugFrom($oldSlug)
    {
        return app($oldSlug->model)->find($oldSlug->entity_id)->slug;
    }

    private function routeGenerator($request)
    {
        return new RouteUrlGenerator(app('url'), $request);
    }
}

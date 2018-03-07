<?php namespace Cviebrock\EloquentSluggable\Tests\Middleware;

use Cviebrock\EloquentSluggable\Middleware\RedirectOnOldSlug;
use Cviebrock\EloquentSluggable\Tests\Models\Post;
use Cviebrock\EloquentSluggable\Tests\TestCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\DB;

class RedirectOnOldSlugTest extends TestCase
{
    public function testRedirectsToCurrentSlug()
    {
        $middleware = new RedirectOnOldSlug;

        $post = Post::create([
            'title' => 'the current slug',
        ]);
        DB::table('old_slugs')->insert([
            'model' => Post::class,
            'entity_id' => $post->id,
            'slug' => 'some-old-slug',
        ]);

        $request = Request::create('/path/to/some-old-slug');
        $request->setRouteResolver(function () use ($request) {
            return tap(new Route(['GET'], '/path/to/{slug}', function () {}), function ($route) use ($request) {
                $route->bind($request);
            });
        });

        $response = $middleware->handle($request, function () {});

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals('/path/to/the-current-slug', $response->getTargetUrl());
    }

    public function testPassesRequestToNextMiddleWareOnOldSlugMiss()
    {
        $middleware = new RedirectOnOldSlug;

        $passed = false;
        $nextCallback = function () use (&$passed) {
            $passed = true;
        };

        $request = Request::create('/path/to/not-a-slug');
        $request->setRouteResolver(function () use ($request) {
            return tap(new Route(['GET'], '/path/to/{slug}', function () {}), function ($route) use ($request) {
                $route->bind($request);
            });
        });

        $middleware->handle($request, $nextCallback);

        $this->assertTrue($passed);
    }
}

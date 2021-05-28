<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();

        view()->composer('front.layouts.navbar', function ($view){
           $view->with('categories', Category::all());
        });
        view()->composer('front.layouts.sidebar', function ($view){
            $view->with('popular_posts' , Post::orderBy('views', 'desc')->limit(3)->get());
            $p = new Post();
            $r=$p->getRecentlyViewed();
            if ($r)
            {
                $view->with('recent_posts', Post::whereIn('id', $r)->get());
            } else {
                $view->with('recent_posts', null);
            }

            $view->with('cats', Category::withCount('posts')->orderBy('posts_count', 'desc')->get());
        });

    }
}

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Course;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('*', function ($view) {
            $courses = Course::where('visible', 1)->get();
            $view->with('courses', $courses);
        });
    }
}

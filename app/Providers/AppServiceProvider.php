<?php

namespace App\Providers;

use App\FormFields\AdditionalContentField;
use App\FormFields\KeyValueJsonFormField;
use App\FormFields\SelectMultipleAuthor;
use App\FormFields\SelectMultipleTag;
use Illuminate\Support\ServiceProvider;
use TCG\Voyager\Facades\Voyager;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Voyager::addFormField(AdditionalContentField::class);
        Voyager::addFormField(SelectMultipleTag::class);
        Voyager::addFormField(SelectMultipleAuthor::class);
        Voyager::addFormField(KeyValueJsonFormField::class);

        $this->app->bind(
            'TCG\Voyager\Http\Controllers\VoyagerBaseController',
            'App\Http\Controllers\ExtendedBreadFormFieldsController'
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Voyager::addAction(\App\Actions\ViewOnSite::class);
    }
}

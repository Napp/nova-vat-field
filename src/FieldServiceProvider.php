<?php

namespace Napp\NovaVatValidation;

use Laravel\Nova\Nova;
use Laravel\Nova\Events\ServingNova;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class FieldServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Nova::serving(function (ServingNova $event) {
            Nova::script('nova-vat-validation', __DIR__.'/../dist/js/field.js');
            Nova::style('nova-vat-validation', __DIR__.'/../dist/css/field.css');
        });

        Validator::extend('vat', function ($attribute, $value, $parameters, $validator) {
            return (new VatValidator())->validate($value);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}

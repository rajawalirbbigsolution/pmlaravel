<?php
 namespace Laravel\Socialite; use Illuminate\Support\ServiceProvider; use Laravel\Socialite\Contracts\Factory; class SocialiteServiceProvider extends ServiceProvider { protected $defer = true; public function register() { $this->app->singleton(Factory::class, function ($app) { return new SocialiteManager($app); }); } public function provides() { return [Factory::class]; } } 
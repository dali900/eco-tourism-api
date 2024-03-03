<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Contracts\RegulationRepositoryInterface', 'App\Repositories\RegulationRepository');
        $this->app->bind('App\Contracts\RegulationTypeRepositoryInterface', 'App\Repositories\RegulationTypeRepository');
        $this->app->bind('App\Contracts\DocumentRepositoryInterface', 'App\Repositories\DocumentRepository');
        $this->app->bind('App\Contracts\DocumentTypeRepositoryInterface', 'App\Repositories\DocumentTypeRepository');
        $this->app->bind('App\Contracts\NewsRepositoryInterface', 'App\Repositories\NewsRepository');
        $this->app->bind('App\Contracts\UserRepositoryInterface', 'App\Repositories\UserRepository');
        $this->app->bind('App\Contracts\ArticleRepositoryInterface', 'App\Repositories\ArticleRepository');
        $this->app->bind('App\Contracts\ArticleTypeRepositoryInterface', 'App\Repositories\ArticleTypeRepository');
        $this->app->bind('App\Contracts\QuestionRepositoryInterface', 'App\Repositories\QuestionRepository');
        $this->app->bind('App\Contracts\QuestionTypeRepositoryInterface', 'App\Repositories\QuestionTypeRepository');
        $this->app->bind('App\Contracts\VideoRepositoryInterface', 'App\Repositories\VideoRepository');
        $this->app->bind('App\Contracts\BannerRepositoryInterface', 'App\Repositories\BannerRepository');
        $this->app->bind('App\Contracts\FreeTrialRepositoryInterface', 'App\Repositories\Plan\FreeTrialRepository');
        $this->app->bind('App\Contracts\SubscriptionRepositoryInterface', 'App\Repositories\Plan\SubscriptionRepository');
        $this->app->bind('App\Contracts\UserProfileRepositoryInterface', 'App\Repositories\Plan\UserProfileRepository');
        $this->app->bind('App\Contracts\DashboardRepositoryInterface', 'App\Repositories\DashboardRepository');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}

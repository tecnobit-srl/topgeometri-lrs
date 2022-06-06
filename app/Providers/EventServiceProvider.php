<?php

namespace App\Providers;

use App\Listeners\ProcessInsertedStatements;
use App\Observers\StatementObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Trax\XapiStore\Events\StatementRecordsInserted;
use Trax\XapiStore\Stores\Statements\Statement;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        StatementRecordsInserted::class => [
            ProcessInsertedStatements::class,
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Statement::observe(StatementObserver::class);
    }
}

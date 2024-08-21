<?php

namespace App\Observers;

use Illuminate\Support\Facades\Log;
use Trax\XapiStore\Stores\Statements\Statement;

class StatementObserver
{
    /**
     * Handle the Statement "created" event.
     *
     * @param  \App\Models\Statement  $statement
     * @return void
     */
    public function created(Statement $statement)
    {
        Log::info('created observer');
    }

    /**
     * Handle the Statement "created" event.
     *
     * @param  \App\Models\Statement  $statement
     * @return void
     */
    public function saved(Statement $statement)
    {
        Log::info('saved observer');
    }

    /**
     * Handle the Statement "updated" event.
     *
     * @param  \App\Models\Statement  $statement
     * @return void
     */
    public function updated(Statement $statement)
    {
        Log::info('updated observer');
    }

    /**
     * Handle the Statement "deleted" event.
     *
     * @param  \App\Models\Statement  $statement
     * @return void
     */
    public function deleted(Statement $statement)
    {
        //
    }

    /**
     * Handle the Statement "restored" event.
     *
     * @param  \App\Models\Statement  $statement
     * @return void
     */
    public function restored(Statement $statement)
    {
        //
    }

    /**
     * Handle the Statement "force deleted" event.
     *
     * @param  \App\Models\Statement  $statement
     * @return void
     */
    public function forceDeleted(Statement $statement)
    {
        //
    }
}

<?php

namespace App\Listeners;

use Trax\XapiStore\Events\StatementRecordsInserted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class ProcessInsertedStatements
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \Trax\XapiStore\Events\StatementRecordsInserted  $event
     * @return void
     */
    public function handle(StatementRecordsInserted $event)
    {
        foreach ($event->statements as $statement) {
            $this->processStatement((array) $statement);
        }
    }

    protected function processStatement($statement)
    {
        $data = json_decode($statement['data'], true);
        $verb = $data['verb']['display']['en-US'];
        Log::info('Processing statement: ' . $verb);
    }
}

<?php

namespace App\Listeners;

use App\Jobs\SendStatementToApi;
use App\Models\Statement;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Trax\XapiStore\Events\StatementRecordsInserted;

class ProcessInsertedStatements
{
    protected $toProcess = ['passed', 'failed', 'started'];
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {

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
            $this->processStatement($statement);
        }
    }

    protected function processStatement($statement)
    {
        $data = $statement['data'];
        $verb = $data->verb->display->{'en-US'};
        if (in_array($verb, $this->toProcess)) {
            $email = Str::remove('mailto:', $data->actor->mbox);

            if(empty($email)) {
                return;
            }

            $id = $data->object->id;

            Log::info('Processing statement: ' . $verb . ' for ' . $email . ' with eg id = ' . $id);

            $processed = new Statement();
            $processed->type = $verb;
            $processed->email = $email;
            $processed->eg_course_id = $id;
            $processed->save();

            SendStatementToApi::dispatch($processed);
        }
    }
}

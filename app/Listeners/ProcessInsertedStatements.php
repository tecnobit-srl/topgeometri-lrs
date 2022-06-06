<?php

namespace App\Listeners;

use App\Models\Statement;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
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
            $this->processStatement($statement);
        }
    }

    protected function processStatement($statement)
    {
        $data = $statement['data'];
        $verb = $data->verb->display->{'en-US'};
        if (in_array($verb, $this->toProcess)) {
            Log::info('Processing statement: ' . $verb);
            $processed = new Statement();
            $processed->type = $verb;
            $processed->email = Str::remove('mailto:', $data->actor->mbox);
            $processed->eg_course_id = $data->context->extensions->{'http://easygenerator/expapi/course/id'};
            $processed->save();
        }
    }
}

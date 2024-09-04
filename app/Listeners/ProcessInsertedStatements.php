<?php

namespace App\Listeners;

use App\Actions\SendStatementToApiAction;
use App\Jobs\SendMissedStatementsToApi;
use App\Models\Statement;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Trax\XapiStore\Events\StatementRecordsInserted;

class ProcessInsertedStatements
{
    protected $toProcess = ['passed', 'failed']; //, 'started'

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

            if (empty($email)) {
                return;
            }

            SendMissedStatementsToApi::dispatch();

            $id =  Str::finish($data->object->id,'/');

            Log::info('Processing statement: '.$verb.' for '.$email.' with eg id = '.$id);

            $processed = new Statement();
            $processed->type = $verb;
            $processed->email = $email;
            $processed->eg_course_id = $id;

            $success = (new SendStatementToApiAction)->execute($processed);

            if (! $success) {
                $processed->save();
            }
        }
    }
}

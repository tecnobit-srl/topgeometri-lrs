<?php

namespace App\Listeners;

use App\Models\Statement;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\InteractsWithQueue;
use App\Actions\SendStatementsToApiAction;
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
            $id = $data->object->id;
            Log::info('Processing statement: ' . $verb . ' for ' . $email . ' with eg id = ' . $id);
            $processed = new Statement();
            $processed->type = $verb;
            $processed->email = $email;
            $processed->eg_course_id = $id;
            $processed->save();

            $toSend = Statement::where('email', $processed->email)
            ->where('eg_course_id', $processed->eg_course_id)
            ->orderBy('created_at', 'asc')
            ->get();

            (new SendStatementsToApiAction())->execute($toSend);
        }
    }
}

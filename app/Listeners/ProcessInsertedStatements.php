<?php

namespace App\Listeners;

use App\Models\Statement;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Trax\XapiStore\Events\StatementRecordsInserted;

class ProcessInsertedStatements
{
    protected $toProcess = ['passed', 'failed', 'started'];
    protected $dev;
    protected $prod;
    protected $devRoute;
    protected $prodRoute;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->dev = config('trax-lrs.lrs_sync.dev');
        if ($this->dev) {
            $this->devRoute = config('trax-lrs.lrs_sync.dev_url') . config('trax-lrs.lrs_sync.endpoint');
        }
        $this->prod = config('trax-lrs.lrs_sync.prod');
        if ($this->prod) {
            $this->prodRoute = config('trax-lrs.lrs_sync.prod_url') . config('trax-lrs.lrs_sync.endpoint');
        }
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

        $toSend = Statement::where('email', $processed->email)
        ->where('eg_course_id', $processed->eg_course_id)
        ->orderBy('created_at', 'asc')
        ->get();

        foreach ($toSend as $send) {
            $success = $this->sendStatement($send);
            if (!$success) {
                break;
            }
        }
    }

    public function sendStatement($statement)
    {
        if ($this->dev) {
            $response = Http::post($this->devRoute, $statement->toArray());
        }
        if ($this->prod) {
            $response = Http::post($this->prodRoute, $statement->toArray());
        }

        Log::info($response);
        if ($response['success']) {
            $statement->delete();
        }

        return false;
        //return $response['success'];

        //TODO: decide if handling noUser, noLesson and noLessonUser errors here or on topgeometri and what to do
    }
}

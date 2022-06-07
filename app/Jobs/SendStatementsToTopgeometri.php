<?php

namespace App\Jobs;

use App\Models\Statement;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendStatementsToTopgeometri implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $dev;
    protected $prod;
    protected $devRoute;
    protected $prodRoute;
    /**
     * Create a new job instance.
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
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $statements = Statement::all();

        foreach ($statements as $statement) {
            $this->processStatement($statement);
        }
    }

    public function processStatement($statement)
    {
        if ($this->dev) {
            $response = Http::post($this->devRoute, $statement->toArray());
        }
        if ($this->prod) {
            $response = Http::post($this->prodRoute, $statement->toArray());
        }

        if ($response['success']) {
            $statement->delete();
        }

        //TODO: decide if handling noUser, noLesson and noLessonUser errors here or on topgeometri and what to do
    }
}

<?php

namespace App\Jobs;

use App\Models\Statement;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendStatementsToTopgeometri implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        //TODO: Send to Topgeometri. To decide if having 1 endpoint that will process every type or having 3 endpoints, 1 for each type.
        //TODO: At the end, if the Api return success, delete the statement from the database.
    }
}

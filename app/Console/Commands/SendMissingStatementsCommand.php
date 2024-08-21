<?php

namespace App\Console\Commands;

use App\Jobs\SendStatementToApi;
use App\Models\Statement;
use Illuminate\Console\Command;

class SendMissingStatementsCommand extends Command
{
    protected $signature = 'send:missing-statements';

    protected $description = 'Command description';

    public function handle(): void
    {
        $statements = Statement::orderBy('created_at', 'asc')->where('created_at', '<', now()->subMinute())->get();

        foreach($statements as $statement){
            SendStatementToApi::dispatch($statement);
        }
    }
}

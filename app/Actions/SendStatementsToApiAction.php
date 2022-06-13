<?php

namespace App\Actions;

use Illuminate\Support\Facades\Http;

class SendStatementsToApiAction
{

    protected $dev;
    protected $prod;
    protected $devRoute;
    protected $prodRoute;

    /**
     * Create a new action instance.
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
     * Execute the action.
     *
     * @param Iterable $statements
     * @return void
     */
    public function execute(Iterable|null $statements)
    {
        foreach ($statements as $statement) {
            $success = $this->processStatement($statement);
            if (!$success) {
                break;
            }
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

        return $response['success'];

        //TODO: decide if handling noUser, noLesson and noLessonUser errors here or on topgeometri and what to do
    }
}

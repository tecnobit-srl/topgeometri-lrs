<?php

namespace App\Actions;

use App\Models\Statement;
use Illuminate\Support\Facades\Http;

class SendStatementToApiAction
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
        if ($this->dev = config('trax-lrs.lrs_sync.dev')) {
            $this->devRoute = config('trax-lrs.lrs_sync.dev_url').config('trax-lrs.lrs_sync.endpoint');
        }

        if ($this->prod = config('trax-lrs.lrs_sync.prod')) {
            $this->prodRoute = config('trax-lrs.lrs_sync.prod_url').config('trax-lrs.lrs_sync.endpoint');
        }
    }

    public function execute(Statement $statement): bool
    {
        if ($this->dev) {
            $response = Http::post($this->devRoute, $statement->toArray());
        }
        if ($this->prod) {
            $response = Http::post($this->prodRoute, $statement->toArray());
        }

        return $response['success'] ?? false;

        //TODO: decide if handling noUser, noLesson and noLessonUser errors here or on corsigeometri and what to do
    }
}

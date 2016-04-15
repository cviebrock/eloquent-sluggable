<?php namespace Tests\Listeners;

class AbortSlugging
{

    public function handle($model, $event)
    {
        echo "SLUGGING ABORTED!\n";
        return false;
    }
}

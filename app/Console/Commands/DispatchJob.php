<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DispatchJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job:dispatch {job} {params?*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $class = '\\App\\Jobs\\' . $this->argument('job');
        $parameters = $this->argument('params') ?? [];
        $job = in_array(FromParameters::class, class_implements($class)) ? $class::fromParameters(...$parameters) : new $class(...$parameters);
        dispatch($job);
        //dispatch(new $class());
    }
}

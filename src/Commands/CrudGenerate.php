<?php

namespace LaravelCode\Crud\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CrudGenerate extends Command
{
    use CrudCommandTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:generate 
    {--always : Overwrite all existing files}
    {--never : Never overwrite existing files}
    {--config= : Location of your custom config file}
    {--output= : full pathname to write the api routes to}
    {--config= : Custom config file}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse json and generate api routes';
    private $path;
    private $config;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Parse JSON and create api.php routes.
     *
     * @throws \Exception
     */
    public function handle()
    {
        Artisan::call('crud:routes');
        Artisan::call('crud:controllers');
        Artisan::call('crud:events');
    }
}

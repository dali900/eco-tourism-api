<?php

namespace App\Console\Commands\File;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\Filesystem;

class EmptyTempFolder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'file:empty-tmp-folder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete files form app storage tmp folder';

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
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if(File::exists(base_path().'/storage/app/tmp')){
            $file = new Filesystem;
            $file->cleanDirectory('storage/app/tmp');
        }
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class CleanDownloads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:downloads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean content public/descargas';



    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $command = 'rm /var/www/app/public/descargas/*';
        exec($command, $out, $ret);
    }
}

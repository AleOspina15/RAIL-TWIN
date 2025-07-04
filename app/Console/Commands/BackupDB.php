<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackupDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup database';



    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $command = 'rm /var/www/app/docker/postgresql/backups/'.env('DB_DATABASE').'.7z';
        exec($command, $out, $ret);

        putenv('PGPASSWORD='.env('DB_PASSWORD'));
        $command = 'pg_dump --host '.env('DB_HOST').' --port 5432 --username postgres --format=p --blobs --encoding UTF8 --verbose --file /var/www/app/docker/postgresql/backups/'.env('DB_DATABASE').'.sql '.env('DB_DATABASE').'';
        exec($command, $out, $ret);
        putenv('PGPASSWORD');

        $command = '7z a -r -bsp2 /var/www/app/docker/postgresql/backups/'.env('DB_DATABASE').'.7z /var/www/app/docker/postgresql/backups/'.env('DB_DATABASE').'.sql';
        exec($command, $out, $ret);

        $command = 'rm /var/www/app/docker/postgresql/backups/'.env('DB_DATABASE').'.sql';
        exec($command, $out, $ret);
    }
}

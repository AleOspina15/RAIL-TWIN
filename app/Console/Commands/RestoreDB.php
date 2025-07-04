<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
class RestoreDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'restore:db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore database';



    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        echo "\n Unzip ".env('DB_DATABASE').".7z";
        $command = '7z e -bsp2 /var/www/app/docker/postgresql/backups/'.env('DB_DATABASE').'.7z -o/var/www/app/docker/postgresql/backups/';
        exec($command, $out, $ret);

        echo "\n Cleaning database.";
        putenv('PGPASSWORD='.env('DB_PASSWORD'));
        $command = 'psql -h '.env('DB_HOST').' -p 5432 -d postgres -U postgres -c "DROP DATABASE '.env('DB_DATABASE').' WITH(FORCE)"';
        exec($command, $out, $ret);
        putenv('PGPASSWORD');

        echo "\n Creating database.";
        putenv('PGPASSWORD='.env('DB_PASSWORD'));
        $command = 'psql -h '.env('DB_HOST').' -p 5432 -d postgres -U postgres -c "CREATE DATABASE '.env('DB_DATABASE').'"';
        exec($command, $out, $ret);
        putenv('PGPASSWORD');

        echo "\n Restoring database.";
        putenv('PGPASSWORD='.env('DB_PASSWORD'));
        $command = 'psql -h '.env('DB_HOST').' -p 5432 -d '.env('DB_DATABASE').' -U postgres -a -f /var/www/app/docker/postgresql/backups/'.env('DB_DATABASE').'.sql';
        exec($command, $out, $ret);
        putenv('PGPASSWORD');

        $command = 'rm /var/www/app/docker/postgresql/backups/'.env('DB_DATABASE').'.sql';
        exec($command, $out, $ret);

        echo "\n Database restored. \n";

    }
}

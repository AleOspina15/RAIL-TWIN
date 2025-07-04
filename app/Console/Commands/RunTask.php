<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RunTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:task';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        for($s=0;$s<60;$s++) {
            $str_sql = "SELECT * FROM sch_aicedronesdi.tasks WHERE state = 'Running'";
            $running = DB::select($str_sql);

            if (count($running) > 0)
                exit;

            $str_sql = "SELECT * FROM sch_aicedronesdi.tasks WHERE state = 'Pending' ORDER BY datetime ASC LIMIT 1";
            $job = DB::select($str_sql);

            if (count($job) > 0) {
                $id = $job[0]->id;
                $project_id = $job[0]->project_id;
                $command = $job[0]->command;
                $message = $job[0]->message;
                $type = $job[0]->type;

                echo "\n $s - Iniciando ejecución tarea: $id\n";
                echo "  Comando: $command\n";

                $str_sql = "UPDATE sch_aicedronesdi.tasks SET state = 'Running' WHERE id = $id";
                $update = DB::select($str_sql);

                if ($type === 'artisan') {
                    $str_command = 'cd /var/www/app/ && '.$command;
                    exec($str_command, $out, $ret);

                    $str_sql = "DELETE FROM sch_aicedronesdi.tasks WHERE id = $id";
                    $delete = DB::select($str_sql);
                }



            }
            sleep(1);
        }

    }
}

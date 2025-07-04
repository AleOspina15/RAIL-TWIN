<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepositoryLog extends Model
{

    protected $table = 'sch_filemanager.repository_log';
    protected $primaryKey = 'id';

    protected $fillable = ['user_id','command','result_raw','result_type','result_data'];


}

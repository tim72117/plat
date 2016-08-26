<?php
namespace Plat\Project;

use Eloquent;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PostFile extends Eloquent {

    protected $table = 'project_post_file';

    protected $fillable = array('post_id', 'file_id');

    public function file()
    {
        return $this->hasOne('Files', 'id', 'file_id');
    }
    
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes,HasFactory;

    protected $guarded = [];

    public function getStatusBadgeAttribute(){
        if($this->status == 'in-progress') return '<span class="badge bg-primary">On Going</span>';
        else if($this->status == 'completed') return '<span class="badge bg-success">Completed</span>';
        return '<span class="badge bg-danger">To Do</span>';
    }
}

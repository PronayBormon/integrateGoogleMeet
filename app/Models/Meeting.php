<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{

    protected $fillable = ['tutor_id', 'student_id', 'title', 'description', 'start_time', 'end_time', 'meet_link', 'google_event_id'];
    public function tutor()
    {
        return $this->belongsTo(User::class, 'tutor_id');
    }
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Fixture extends Model
{
    use HasFactory;

    protected $table = 'matches';

    protected $fillable = [
        'slug','title','team1_id','team2_id','best_of','start_at','status',
        'team1_score','team2_score','winner_team_id','stage','league','vod_url','notes'
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'best_of' => 'integer',
        'team1_score' => 'integer',
        'team2_score' => 'integer',
    ];

    public function team1(){ return $this->belongsTo(Team::class, 'team1_id'); }
    public function team2(){ return $this->belongsTo(Team::class, 'team2_id'); }
    public function winner(){ return $this->belongsTo(Team::class, 'winner_team_id'); }
}

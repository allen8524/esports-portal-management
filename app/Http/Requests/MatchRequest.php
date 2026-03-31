<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MatchRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array {
        return [
            'team1_id' => ['required','different:team2_id','exists:teams,id'],
            'team2_id' => ['required','exists:teams,id'],
            'best_of'  => ['required','in:1,3,5,7'],
            'start_at' => ['nullable','date'],
            'status'   => ['required','in:scheduled,live,finished,canceled'],
            'team1_score' => ['nullable','integer','min:0'],
            'team2_score' => ['nullable','integer','min:0'],
            'winner_team_id' => ['nullable','in:'.implode(',', [request('team1_id'), request('team2_id')])],
            'title'    => ['nullable','string','max:255'],
            'stage'    => ['nullable','string','max:100'],
            'league'   => ['nullable','string','max:100'],
            'vod_url'  => ['nullable','url'],
            'notes'    => ['nullable','string'],
            'slug'     => ['nullable','string','max:255'],
        ];
    }
}

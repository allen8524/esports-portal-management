<?php 

namespace App\Http\Controllers;

use App\Http\Requests\MatchRequest;
use App\Models\Fixture;   // ← matches 테이블 모델
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware as ControllerMiddleware;

class MatchController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new ControllerMiddleware('auth', except: ['index', 'show']),
            new ControllerMiddleware('admin', except: ['index', 'show']),
        ];
    }

    /**
     * DB에 이미 저장된 리그 목록 (자동완성용)
     */
    protected function leagueList()
    {
        return Fixture::query()
            ->select('league')
            ->whereNotNull('league')
            ->where('league', '!=', '')
            ->distinct()
            ->orderBy('league')
            ->pluck('league');
    }

    /**
     * 스테이지 템플릿 목록
     */
    protected function stageTemplates(): array
    {
        return [
            // Regional / 국내 리그
            'Regional Split',
            'Regular Season',
            'Spring Split',
            'Summer Split',
            'Playoffs',
            'Playoffs Round 1',
            'Playoffs Round 2',
            'Playoffs Round 3',
            'Regional Finals',
            'Promotion / Relegation',

            // 사용자가 말한 First Stand 쪽
            'First Stand',
            'First Stand – Round Robin',
            'First Stand – Knockout',

            // MSI
            'MSI Play-In Stage',
            'MSI Bracket Stage',
            'MSI Knockout Stage',
            'MSI Finals',

            // Worlds
            'Worlds Play-In Stage',
            'Worlds Group Stage',
            'Worlds Swiss Stage',
            'Worlds Knockout Stage',
            'Worlds Quarterfinals',
            'Worlds Semifinals',
            'Worlds Finals',
        ];
    }

    public function index(Request $req)
    {
        $q      = $req->input('q');
        $teamId = $req->input('team_id');
        $status = $req->input('status');
        $from   = $req->input('from');
        $to     = $req->input('to');

        $list = Fixture::query()
            ->with(['team1', 'team2', 'winner'])
            ->when($q, fn($qq) => $qq->where(function ($w) use ($q) {
                $w->where('title', 'like', "%{$q}%")
                    ->orWhere('league', 'like', "%{$q}%")
                    ->orWhere('stage', 'like', "%{$q}%");
            }))
            ->when($teamId, fn($qq) => $qq->where(fn($w) => $w
                ->where('team1_id', $teamId)->orWhere('team2_id', $teamId)))
            ->when($status, fn($qq) => $qq->where('status', $status))
            ->when($from, fn($qq) => $qq->whereDate('start_at', '>=', $from))
            ->when($to, fn($qq) => $qq->whereDate('start_at', '<=', $to))
            ->orderByRaw("CASE status
                  WHEN 'live' THEN 0
                  WHEN 'scheduled' THEN 1
                  WHEN 'finished' THEN 2
                  ELSE 3 END")
            ->orderBy('start_at', 'asc')
            ->paginate(5);

        return view('matches.index', [
            'list'   => $list,
            'teams'  => Team::orderBy('name')->get(['id', 'name']),
            'q'      => $q,
            'team_id'=> $teamId,
            'status' => $status,
            'from'   => $from,
            'to'     => $to,
        ]);
    }

    public function create()
    {
        return view('matches.create', [
            'match'   => new Fixture(), // 폼에서 $match 쓰고 있으니까 빈 모델 넘김
            'teams'   => Team::orderBy('name')->get(['id', 'name']),
            'leagues' => $this->leagueList(),
            'stages'  => $this->stageTemplates(),
        ]);
    }

    public function store(MatchRequest $req)
    {
        $data = $req->validated();
        $data['slug'] = $data['slug'] ?? Str::slug(($data['title'] ?? 'match') . '-' . now()->format('YmdHis'));

        if (($data['status'] ?? null) === 'finished' && empty($data['winner_team_id'])) {
            $data['winner_team_id'] =
                $data['team1_score'] > $data['team2_score'] ? $data['team1_id'] :
                ($data['team2_score'] > $data['team1_score'] ? $data['team2_id'] : null);
        }

        $m = Fixture::create($data);
        return redirect()->route('matches.show', $m)->with('success', '경기 등록 완료');
    }

    public function show(Fixture $match)
    {
        $match->load(['team1', 'team2', 'winner']);
        return view('matches.show', compact('match'));
    }

    public function edit(Fixture $match)
    {
        return view('matches.edit', [
            'match'   => $match->load(['team1', 'team2', 'winner']),
            'teams'   => Team::orderBy('name')->get(['id', 'name']),
            'leagues' => $this->leagueList(),
            'stages'  => $this->stageTemplates(),
        ]);
    }

    public function update(MatchRequest $req, Fixture $match)
    {
        $data = $req->validated();

        if (($data['status'] ?? null) === 'finished' && empty($data['winner_team_id'])) {
            $data['winner_team_id'] =
                $data['team1_score'] > $data['team2_score'] ? $data['team1_id'] :
                ($data['team2_score'] > $data['team1_score'] ? $data['team2_id'] : null);
        }

        $match->update($data);
        return redirect()->route('matches.show', $match)->with('success', '수정 완료');
    }

    public function destroy(Fixture $match)
    {
        $match->delete();
        return redirect()->route('matches.index');
    }
}

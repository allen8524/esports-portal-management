<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware as ControllerMiddleware;

class RankingsController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new ControllerMiddleware('auth', except: ['index', 'show']),
            new ControllerMiddleware('admin', except: ['index', 'show']),
        ];
    }
    
    public function index(Request $request)
    {
        $league     = $request->input('league');            // 예: LCK
        $stage      = $request->input('stage');             // 예: Regular
        $includeAll = $request->boolean('include_all');     // 경기 0팀 포함 여부
        $sort       = $request->input('sort', 'wins');      // wins|winrate|diff|name|sf|sa
        $dir        = strtolower($request->input('dir','desc')) === 'asc' ? 'asc' : 'desc';

        // === 콤보박스용 리그 / 스테이지 옵션 ===
        $leagueOptions = Cache::remember('rankings:leagueOptions', 300, function () {
            return DB::table('matches')
                ->whereNotNull('league')
                ->distinct()
                ->orderBy('league')
                ->pluck('league');
        });

        $stageOptions = Cache::remember('rankings:stageOptions', 300, function () {
            return DB::table('matches')
                ->whereNotNull('stage')
                ->distinct()
                ->orderBy('stage')
                ->pluck('stage');
        });

        // 1) 홈/원정 → 단일 스키마로 정규화 (finished만 집계)
        $home = DB::table('matches')
            ->when($league, fn($q) => $q->where('league', $league))
            ->when($stage,  fn($q) => $q->where('stage',  $stage))
            ->where('status', 'finished')
            ->selectRaw('team1_id as team_id,
                         CASE WHEN team1_score > team2_score THEN 1 ELSE 0 END as win,
                         CASE WHEN team1_score < team2_score THEN 1 ELSE 0 END as loss,
                         team1_score as sf, team2_score as sa');

        $away = DB::table('matches')
            ->when($league, fn($q) => $q->where('league', $league))
            ->when($stage,  fn($q) => $q->where('stage',  $stage))
            ->where('status', 'finished')
            ->selectRaw('team2_id as team_id,
                         CASE WHEN team2_score > team1_score THEN 1 ELSE 0 END as win,
                         CASE WHEN team2_score < team1_score THEN 1 ELSE 0 END as loss,
                         team2_score as sf, team1_score as sa');

        $base = $home->unionAll($away);

        // 2) 팀별 집계
        $agg = DB::query()->fromSub($base, 'm')
            ->selectRaw('team_id,
                         COUNT(*) as games,
                         SUM(win)  as wins,
                         SUM(loss) as losses,
                         SUM(sf)   as score_for,
                         SUM(sa)   as score_against,
                         SUM(sf) - SUM(sa) as diff,
                         ROUND(SUM(win) * 100.0 / NULLIF(COUNT(*),0), 1) as win_rate')
            ->groupBy('team_id');

        // 3) teams와 조인 (경기 0팀 포함 옵션)
        $q = DB::table('teams');
        $includeAll
            ? $q->leftJoinSub($agg, 'agg', 'agg.team_id', '=', 'teams.id')
            : $q->joinSub($agg, 'agg', 'agg.team_id', '=', 'teams.id');

        $q->selectRaw('
            teams.id   as team_id,
            teams.name as team_name,
            teams.logo_url as logo_url,
            COALESCE(agg.games,0)         as games,
            COALESCE(agg.wins,0)          as wins,
            COALESCE(agg.losses,0)        as losses,
            COALESCE(agg.score_for,0)     as score_for,
            COALESCE(agg.score_against,0) as score_against,
            COALESCE(agg.diff,0)          as diff,
            COALESCE(agg.win_rate,0)      as win_rate
        ');

        // 4) 정렬 + 타이브레이커
        switch ($sort) {
            case 'winrate':
                $q->orderBy('win_rate', $dir)
                  ->orderBy('wins', 'desc')
                  ->orderBy('diff', 'desc')
                  ->orderBy('score_for', 'desc')
                  ->orderBy('team_name', 'asc');
                break;
            case 'diff':
                $q->orderBy('diff', $dir)
                  ->orderBy('wins', 'desc')
                  ->orderBy('win_rate', 'desc')
                  ->orderBy('score_for', 'desc')
                  ->orderBy('team_name', 'asc');
                break;
            case 'sf':
                $q->orderBy('score_for', $dir)
                  ->orderBy('wins', 'desc')
                  ->orderBy('diff', 'desc')
                  ->orderBy('team_name', 'asc');
                break;
            case 'sa':
                $q->orderBy('score_against', $dir)
                  ->orderBy('wins', 'asc')
                  ->orderBy('diff', 'desc')
                  ->orderBy('team_name', 'asc');
                break;
            case 'name':
                $q->orderBy('team_name', $dir);
                break;
            case 'wins':
            default:
                $q->orderBy('wins', $dir)
                  ->orderBy('diff', 'desc')
                  ->orderBy('score_for', 'desc')
                  ->orderBy('team_name', 'asc');
        }

        // 5) 캐시 (5분)
        $cacheKey = 'rankings:v3:' . md5(json_encode([$league,$stage,$includeAll,$sort,$dir]));
        $standings = Cache::remember($cacheKey, 300, fn () => $q->get());

        return view('rankings.index', [
            'standings'     => $standings,
            'league'        => $league,
            'stage'         => $stage,
            'leagueOptions' => $leagueOptions,
            'stageOptions'  => $stageOptions,
        ]);
    }
}

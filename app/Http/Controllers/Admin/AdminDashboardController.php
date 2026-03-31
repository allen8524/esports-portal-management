<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Fixture;
use App\Models\Player;
use App\Models\Team;
use App\Models\News;
use App\Models\PatchNote;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // ---- 상단 요약 카드용 숫자들 ----
        $accountCount = Account::count();
        $adminCount   = Account::where('is_admin', 1)->count();

        $teamCount    = Team::count();
        $playerCount  = Player::count();

        $totalMatches   = Fixture::count();
        $liveMatchCount = Fixture::where('status', 'live')->count();
        $scheduledCount = Fixture::where('status', 'scheduled')->count();
        $finishedCount  = Fixture::where('status', 'finished')->count();

        $newsCount    = News::count();
        $patchCount   = PatchNote::count();

        // ---- 최근/다가오는 리스트들 ----
        $recentAccounts = Account::orderByDesc('created_at')
            ->limit(5)
            ->get(['id','name','email','is_admin','created_at']);

        $upcomingMatches = Fixture::with(['team1','team2'])
            ->where('status', 'scheduled')
            ->where('start_at', '>=', now())
            ->orderBy('start_at')
            ->limit(5)
            ->get();

        $recentMatches = Fixture::with(['team1','team2','winner'])
            ->where('status', 'finished')
            ->orderByDesc('start_at')
            ->limit(5)
            ->get();

        $recentNews = News::published()
            ->orderByDesc('published_at')
            ->limit(5)
            ->get(['id','title','slug','published_at']);

        $recentPatchNotes = PatchNote::published()
            ->orderByDesc('published_at')
            ->limit(5)
            ->get(['id','game','version','title','slug','published_at']);

        // ---- 리그별 경기 수 Top 5 ----
        $matchByLeague = Fixture::select('league', DB::raw('COUNT(*) as cnt'))
            ->groupBy('league')
            ->orderByDesc('cnt')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'accountCount', 'adminCount',
            'teamCount', 'playerCount',
            'totalMatches', 'liveMatchCount', 'scheduledCount', 'finishedCount',
            'newsCount', 'patchCount',
            'recentAccounts', 'upcomingMatches', 'recentMatches',
            'recentNews', 'recentPatchNotes', 'matchByLeague'
        ));
    }
}

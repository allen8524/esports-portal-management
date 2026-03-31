<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware as ControllerMiddleware;

class TeamController extends Controller implements HasMiddleware
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
        $q = $request->input('q');

        $teams = Team::when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('slug', 'like', "%{$q}%")
                        ->orWhere('region', 'like', "%{$q}%");
                });
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('teams.index', compact('teams','q'));
    }

    public function create()
    {
        return view('teams.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'slug'       => 'nullable|string|max:120|unique:teams,slug',
            'region'     => 'nullable|string|max:10',
            'founded_at' => 'nullable|date',
            'logo'       => 'nullable|image|max:2048',
            'is_active'  => 'nullable|boolean',
        ]);

        // slug 자동 생성 (미입력 시)
        $data['slug'] = $data['slug'] ?? Str::slug($request->name . '-' . Str::random(4));

        // 로고 업로드 (public 디스크/teams 폴더)
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('teams', 'public');
            $data['logo_url'] = $path;
        }

        Team::create($data);

        return redirect()->route('teams.index')->with('success', '팀이 등록되었습니다.');
    }

	public function show(Team $team)
	{
		// 팀 소속 선수 목록까지 함께 로드 (닉네임 순)
		$team->load(['players' => function ($q) {
			$q->orderBy('ign');
		}]);

		return view('teams.show', compact('team'));
	}

	// ... 상단 use/클래스 선언은 그대로

	public function edit(Team $team)
	{
		return view('teams.edit', compact('team'));
	}

	public function update(Request $request, Team $team)
	{
		$data = $request->validate([
			'name'       => 'required|string|max:100',
			'slug'       => 'nullable|string|max:120|unique:teams,slug,'.$team->id,
			'region'     => 'nullable|string|max:10',
			'founded_at' => 'nullable|date',
			'logo'       => 'nullable|image|max:2048',
			'is_active'  => 'nullable|boolean',
		]);

		// 미입력 시 slug 자동 유지/생성
		if (empty($data['slug'])) {
			$data['slug'] = $team->slug ?: Str::slug($request->name . '-' . Str::random(4));
		}

		// 로고 업로드 (새 파일이 들어온 경우만)
		if ($request->hasFile('logo')) {
			$path = $request->file('logo')->store('teams', 'public');

			$data['logo_url'] = $path;
		}

    $team->update($data);

    return redirect()->route('teams.show', $team)->with('success', '팀 정보가 수정되었습니다.');
}

public function destroy(Team $team)
{
    // 로고 물리 파일도 함께 제거 (public/storage/teams/xxx 형식 처리)
    if ($team->logo_url) {
        $rel = null;

        if (preg_match('#(?:^|/)storage/(.+)$#', $team->logo_url, $matches)) {
            $rel = $matches[1];
        } elseif (!preg_match('#^https?://#', $team->logo_url)) {
            $rel = ltrim($team->logo_url, '/');
        }

        if ($rel) {
            Storage::disk('public')->delete($rel);
        }
    }

    $team->delete();

    return redirect()->route('teams.index')->with('success', '팀이 삭제되었습니다.');
}

}

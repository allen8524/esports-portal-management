<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware as ControllerMiddleware;

class PlayerController extends Controller implements HasMiddleware
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
        $q      = $request->input('q');
        $role   = $request->input('role');
        $teamId = $request->input('team_id');
        $active = $request->boolean('only_active');

        $players = Player::query()
            ->when($q, fn($qq) =>
                $qq->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                      ->orWhere('ign', 'like', "%{$q}%")
                      ->orWhere('slug', 'like', "%{$q}%");
                })
            )
            ->when($role, fn($qq) => $qq->where('role', $role))
            ->when($teamId, fn($qq) => $qq->where('team_id', $teamId))
            ->when($active, fn($qq) => $qq->where('is_active', true))
            ->with('team')
            ->orderBy('ign')
            ->paginate(12)
            ->withQueryString();

        $roles = ['Top','Jungle','Mid','ADC','Support'];
        $teams = Team::orderBy('name')->get(['id','name']);

        return view('players.index', compact('players','q','role','teamId','active','roles','teams'));
    }

    public function create()
    {
        $teams = Team::orderBy('name')->get(['id','name']);
        return view('players.create', compact('teams'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:100',
            'ign'       => 'required|string|max:50',
            'slug'      => 'nullable|string|max:100|unique:players,slug',
            'role'      => 'nullable|string|max:20',
            'country'   => 'nullable|string|size:2',
            'birthdate' => 'nullable|date',
            'team_id'   => 'nullable|exists:teams,id',
            'photo'     => 'nullable|image|max:2048',
            'photo_url' => 'nullable|url|max:2048',
            'joined_at' => 'nullable|date',
            'left_at'   => 'nullable|date|after_or_equal:joined_at',
            'is_active' => 'nullable|boolean',
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($request->ign.'-'.Str::random(4));
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('players', 'public'); // storage/app/public/players/...
            $data['photo_path'] = $path;
            $data['photo_url'] = null;
        }

        Player::create($data);

        return redirect()->route('players.index')->with('success', 'Player created');
    }

    public function show(Player $player)
    {
        $player->load('team');
        return view('players.show', compact('player'));
    }

    public function edit(Player $player)
    {
        $teams = Team::orderBy('name')->get(['id','name']);
        return view('players.edit', compact('player','teams'));
    }

    public function update(Request $request, Player $player)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:100',
            'ign'       => 'required|string|max:50',
            'slug'      => 'nullable|string|max:100|unique:players,slug,'.$player->id,
            'role'      => 'nullable|string|max:20',
            'country'   => 'nullable|string|size:2',
            'birthdate' => 'nullable|date',
            'team_id'   => 'nullable|exists:teams,id',
            'photo'     => 'nullable|image|max:2048',
            'photo_url' => 'nullable|url|max:2048',
            'joined_at' => 'nullable|date',
            'left_at'   => 'nullable|date|after_or_equal:joined_at',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('players', 'public');
            $data['photo_path'] = $path;
            $data['photo_url'] = null;
        } elseif (!empty($data['photo_url'])) {
            $data['photo_path'] = null;
        }

        $player->update($data);

        return redirect()->route('players.show', $player)->with('success', 'Player updated');
    }

    public function destroy(Player $player)
    {
        $player->delete();
        return redirect()->route('players.index')->with('success', 'Player deleted');
    }
}

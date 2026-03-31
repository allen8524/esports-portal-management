<?php

// app/Http/Controllers/PatchNoteController.php
namespace App\Http\Controllers;

use App\Models\PatchNote;
use Illuminate\Http\Request;

class PatchNoteController extends Controller
{
    // 목록: /patch-notes?game=lol&q=14.22
    public function index(Request $request)
    {
        $game = $request->string('game')->toString() ?: 'lol';
        $q    = $request->string('q')->toString();

        $notes = PatchNote::query()
            ->game($game)
            ->published()
            ->when($q, fn($qq) => $qq->where(function ($w) use ($q) {
                $w->where('title', 'like', "%{$q}%")
                  ->orWhere('version', 'like', "%{$q}%");
            }))
            ->latest('published_at')
            ->paginate(12)
            ->withQueryString();

        return view('patch_notes.index', [
            'notes' => $notes,
            'activeGame' => $game,
            'q' => $q,
        ]);
    }

    // 상세: /patch-notes/{slug}
    public function show(PatchNote $patchNote)
    {
        return view('patch_notes.show', ['note' => $patchNote]);
    }
}

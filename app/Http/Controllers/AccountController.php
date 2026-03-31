<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function show(Request $request)
    {
        $account = $request->user();
        return view('account.show', compact('account'));
    }

    public function edit(Request $request)
    {
        $account = $request->user();
        return view('account.edit', compact('account'));
    }

    public function update(Request $request)
    {
        $account = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:accounts,email,' . $account->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $account->name = $data['name'];
        $account->email = $data['email'];

        if (!empty($data['password'])) {
            $account->password = Hash::make($data['password']);
        }

        $account->save();

        return redirect()->route('account.show')->with('status', '계정 정보가 수정되었습니다.');
    }
}

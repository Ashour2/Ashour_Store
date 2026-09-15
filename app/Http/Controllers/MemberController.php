<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MemberController extends Controller
{
    /**
     * Store a community-join request from the public site.
     * Uses the "join" error bag so errors don't clash with other forms.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'country_code' => 'required|string|max:6',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator, 'join')
                ->withInput()
                ->with('open_join', true);
        }

        $data = $validator->validated();
        $number = preg_replace('/[^0-9]/', '', $data['country_code'] . $data['phone']);

        Member::create([
            'name' => $data['name'],
            'phone' => $number,
            'email' => $data['email'] ?? null,
        ]);

        return redirect()->back()->with('member_joined', true);
    }
}

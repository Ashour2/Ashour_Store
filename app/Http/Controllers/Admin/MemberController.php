<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index()
    {
        $members = Member::latest('id')->paginate(15);

        return view('admin.members.index', compact('members'));
    }

    public function destroy(Member $member)
    {
        $member->delete();

        return redirect()->route('admin.customers')
            ->with('msg', 'Member deleted successfully')->with('type', 'info');
    }
}

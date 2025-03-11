<?php
namespace App\Http\Controllers;


use App\Models\Member;


class MemberController extends Controller
{

    public function index()
    {
        $members = Member::all(); 

        return view('member', compact('members'));
    }
    

    public function delete($id)
    {
        $member = Member::findOrFail($id);
        $member->delete();

        return redirect()->route('members.index')->with('success', 'Kullanıcı silindi!');
    }
}
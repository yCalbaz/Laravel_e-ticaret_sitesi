<?php
namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;

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

        return response()->json(['success' => 'Kullanıcı silindi!'], 200);
    }

    public function updateAuthority(Request $request, $id)
    {
        $request->validate([
            'authority_id' => 'required|integer',
        ]);

        $member = Member::findOrFail($id);
        $member->authority_id = $request->authority_id;
        $member->save();

        return response()->json(['success' => 'Yetki güncellendi.']);
    }

}
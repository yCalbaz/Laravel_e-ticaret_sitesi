<?php
namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Response as HttpResponse;

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
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function index() {
        $comments = DB::table('comments')
            ->Join('users', 'users.id', 'comments.user_id')
            ->select('comments.*', 'users.name')
            ->orderBy('comments.id', 'DESC')
            ->paginate(5);
        return view('home', compact('comments'));
    }

    public function store(Request $request) {
        DB::table('comments')->insert([
            'user_id' => Auth::user()->id,
            'comment' => $request->comment,
        ]);
        return redirect()->route('dashboard');
    }

    public function update(Request $request) {
        $id = $request->id;
        DB::table('comments')->where('id', $id)->update([
            'comment' => $request->comment,
        ]);
        return redirect()->route('dashboard');
    }

    public function destroy($id) {
        DB::table('comments')->where('id', $id)->delete();
        return redirect()->route('dashboard');
    }
}

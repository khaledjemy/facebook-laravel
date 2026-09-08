<?php
namespace App\Http\Controllers;
use App\Group;
use App\Page;
use Illuminate\Http\Request;
class CommunityController extends Controller {
    public function index() { return view('community', ['pages'=>Page::latest()->get(), 'groups'=>Group::latest()->get()]); }
    public function storePage(Request $request) { $data=$request->validate(['name'=>'required|string|max:120','description'=>'nullable|string|max:500']); Page::create($data+['user_id'=>auth()->id()]); return back()->with('success','تم إنشاء الصفحة'); }
    public function storeGroup(Request $request) { $data=$request->validate(['name'=>'required|string|max:120','description'=>'nullable|string|max:500']); Group::create($data+['user_id'=>auth()->id()]); return back()->with('success','تم إنشاء الجروب'); }
}

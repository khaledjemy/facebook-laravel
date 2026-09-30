<?php
namespace App\Http\Controllers;
use App\Group;
use App\Page;
use Illuminate\Http\Request;
class CommunityController extends Controller
{
    private const PAGE_SIZE = 12;

    public function index()
    {
        return view('community', [
            'pages' => Page::with('owner')->latest('created_at')->orderByDesc('id')->paginate(self::PAGE_SIZE, ['*'], 'pages_page'),
            'groups' => Group::with('owner')->latest('created_at')->orderByDesc('id')->paginate(self::PAGE_SIZE, ['*'], 'groups_page'),
        ]);
    }

    public function storePage(Request $request)
    {
        $data = $request->validateWithBag('page', [
            'name' => 'required|string|max:120',
            'description' => 'nullable|string|max:500',
        ]);

        Page::create($data + ['user_id' => $request->user()->id]);

        return back()->with('success', 'تم إنشاء الصفحة');
    }

    public function storeGroup(Request $request)
    {
        $data = $request->validateWithBag('group', [
            'name' => 'required|string|max:120',
            'description' => 'nullable|string|max:500',
        ]);

        Group::create($data + ['user_id' => $request->user()->id]);

        return back()->with('success', 'تم إنشاء الجروب');
    }
}

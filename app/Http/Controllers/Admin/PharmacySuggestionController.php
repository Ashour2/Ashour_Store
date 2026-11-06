<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\PharmacySuggestion;
use App\Services\AdminNotificationService;
use Illuminate\Http\Request;
class PharmacySuggestionController extends Controller {
 public function store(Request $request){$user=auth()->user();abort_unless($user->isPharmacist() && $user->pharmacy_id,403);$data=$request->validate(['title'=>'required|string|max:120','message'=>'required|string|max:2000']);$suggestion=PharmacySuggestion::create($data+['pharmacy_id'=>$user->pharmacy_id,'user_id'=>$user->id]);app(AdminNotificationService::class)->forManagers('suggestion','اقتراح جديد من صيدلية',"{$user->pharmacy->trans_name}: {$suggestion->title}",route('admin.pharmacy-suggestions.show',$suggestion));return back()->with('msg','تم إرسال اقتراحك لمدير المنصة.')->with('type','success');}

 public function show(PharmacySuggestion $suggestion){
    abort_unless(auth()->user()->isSuperAdmin(), 403);
    $suggestion->load(['pharmacy', 'user']);
    return view('admin.pharmacy-suggestions.show', compact('suggestion'));
 }

 public function updateStatus(Request $request, PharmacySuggestion $suggestion){
    abort_unless($request->user()->isSuperAdmin(), 403);
    $data=$request->validate(['status'=>'required|in:new,reviewing,completed,declined']);
    $suggestion->update($data);
    return back()->with('msg','تم تحديث حالة الاقتراح.')->with('type','success');
 }
}

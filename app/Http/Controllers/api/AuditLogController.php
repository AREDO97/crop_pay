<?php

namespace App\Http\Controllers\api;
use App\Services\YoolaSmsService;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Services\helloWorld;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    // view all logs
    public function index(Request $request)
    {
        $admin = $request->user();

    if ($admin->role !== 'admin' && $admin->role !== 'super_admin') {
        abort(403, 'Unauthorised action');
    }
    // logs
    $logs = AuditLog::where('status','active')->latest()->get();
    // response
     return response()->json($logs);
    }
    // delete log
    public function destroy(Request $request,AuditLog $log)
    {
        $admin = $request->user();

    if ($admin->role !== 'admin' && $admin->role !== 'super_admin') {
        abort(403, 'Unauthorised action');
    }
    // logs
    $log->delete();
    // response
     return response()->json([
        'message' => 'Audit log deleted successifully'
     ]);
    }
    // archive logs
    public function archive(Request $request,AuditLog $log)
    {
          $admin = $request->user();

    if ($admin->role !== 'admin' && $admin->role !== 'super_admin') {
        abort(403, 'Unauthorised action');
    }
    // logs
    $log->update([
        'status' => 'archived'
    ]);
    // response
     return response()->json([
        'message' => 'Audit log archived successifully'
     ]);
    }
    // delete old logs
   public function clearAll()
{
    AuditLog::where('created_at', '<', now()->subHours(24))
        ->delete();

    return response()->json([
        'message' => 'Old logs deleted successfully'
    ]);
}

}

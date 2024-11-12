<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ReportRequest;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function report(ReportRequest $request)
    {
        $user = User::find($request->user()->uuid);
        $create = new Report();
        $create->user_id = $user->uuid;
        $create->type = $request->type;
        $create->reported_id = $request->reported_id;
        $create->message = $request->message;
        $create->save();

        return response()->json([
            'status' => true,
            'action' =>  'Report Added',
        ]);
    }
}

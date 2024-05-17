<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ReportRequest;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function report(ReportRequest $request)
    {
        $create = new Report();
        $create->user_id = $request->user_id;
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

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Leave;
use App\Models\LeaveBalance;
use Carbon\Carbon;
class LeaveApprovalsController extends Controller
{
    public function index()
    {
        return Inertia::render('Approvals', [
            'leaves' => Leave::with(['user', 'approvedBy', 'leaveBalance'])->get(),
        ]);
        
    }

    public function approve(Leave $leave)
    {
        $leave->update(['status' => 'approved']);
        $leave->update(['approved_by' => auth()->user()->id]);
        $leave->update(['approved_at' => now()]);
        return redirect()->route('leave-approvals.index');
    }

    public function reject(Leave $leave)
    {
        $leave->update(['status' => 'rejected']);
        $leave->update(['rejected_by' => auth()->user()->id]);
        $leave->update(['rejected_at' => now()]);

        $totalDays = Carbon::parse($leave->start_date)->diffInDays(Carbon::parse($leave->end_date)) + 1;

        // restore leave balance
        if($leave->type === 'annual') {
            $leaveBalance = LeaveBalance::where('user_id', $leave->user_id)->first();
            $leaveBalance->update([
                'annual_leave' => $leaveBalance->annual_leave + $totalDays,
            ]);
        }
        if($leave->type === 'sick') {
            $leaveBalance = LeaveBalance::where('user_id', $leave->user_id)->first();
            $leaveBalance->update([
                'sick_leave' => $leaveBalance->sick_leave + $totalDays,
            ]);
        }
        if($leave->type === 'other') {
            $leaveBalance = LeaveBalance::where('user_id', $leave->user_id)->first();
            $leaveBalance->update([
                'other_leave' => $leaveBalance->other_leave + $totalDays,
            ]);
        }
        return redirect()->route('leave-approvals.index');
    }


}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Leave;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\LeaveBalance;
use Carbon\Carbon;

class LeaveController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $user = Auth::user();

        $overlapping = Leave::where('user_id', $user->id)
            ->where('status', '!=', 'rejected')
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                    ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                    ->orWhere(function ($subQuery) use ($request) {
                        $subQuery->where('start_date', '<=', $request->start_date)
                            ->where('end_date', '>=', $request->end_date);
                    });
            })
            ->exists();

        if ($overlapping) {
            return back()->withErrors('You have leave overlapping with the requested leave');
        }
        $totalDays = Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date));

        $leaveBalances = LeaveBalance::where('user_id', $user->id)->first();
        if($request->type === 'annual') {
            if($leaveBalances->annual_leave < $totalDays) {
                return back()->withErrors('You do not have sufficient annual leave balance');
            }
        }
        if($request->type === 'sick') {
            if($leaveBalances->sick_leave < $totalDays) {
                return back()->withErrors('You do not have sufficient sick leave balance');
            }
        }
        if($request->type === 'other') {
            if($leaveBalances->other_leave < $totalDays) {
                return back()->withErrors('You do not have sufficient other leave balance');
            }
        }

        DB::beginTransaction();

        try {

            Leave::create([
                'user_id' => $user->id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'type' => $request->type,
                'reason' => $request->reason,
            ]);


            $leaveBalances = LeaveBalance::where('user_id', $user->id)->first();

            if($request->type === 'annual') {   
            $leaveBalances->update([
                'annual_leave' => $leaveBalances->annual_leave - $totalDays,
                ]);
            }

            if($request->type === 'sick') {
                $leaveBalances->update([
                    'sick_leave' => $leaveBalances->sick_leave - $totalDays,
                ]);
            }
            if($request->type === 'other') {
                $leaveBalances->update([
                    'other_leave' => $leaveBalances->other_leave - $totalDays,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('An error occurred while processing your request');
        }

        return redirect()->route('dashboard');
    }
}

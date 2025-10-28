<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Application;
use App\Models\ApplicationBreak;
use App\Models\AttendanceRecord;
use App\Models\AttendanceBreak;
use App\Http\Requests\CorrectionRequest;

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->attendance_status === '退勤済') {
            $attendance = AttendanceRecord::where('user_id', $user->id)
                ->whereDate('date', now()->format('Y-m-d'))
                ->first();

            if (! $attendance) {
                $user->attendance_status = '勤務外';
                $user->save();
            }
        }

        $now = new \DateTime();
        $week = [
            0 => '日', 1 => '月', 2 => '火', 3 => '水',
            4 => '木', 5 => '金', 6 => '土',
        ];
        $weekday = $week[$now->format('w')];
        $formattedDate = $now->format("Y年m月d日({$weekday})");
        $formattedTime = $now->format('H:i');

        return view(
            'user/attendance-register',
            compact('formattedDate', 'formattedTime', 'user')
        );
    }
}

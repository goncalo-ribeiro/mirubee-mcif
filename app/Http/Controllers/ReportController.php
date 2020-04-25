<?php

namespace App\Http\Controllers;

use App\Report;
use App\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    
    public function getUserReports()
    {
        $user = Auth::user();

        return $user->reports->sortBy('time')->values()->all();;
    }

}

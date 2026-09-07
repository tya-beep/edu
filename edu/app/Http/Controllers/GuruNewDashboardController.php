<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Support\Facades\Auth;

class GuruNewDashboardController extends Controller
{
    public function index()
    {
        return view('gurunew.dashboard');
    }

    public function inbox()
    {
        $guru = Auth::guard('guru_new')->user();

        $documents = Document::where('gn_id', $guru->gn_id)
            ->orderBy('dateIssued', 'desc')
            ->get();

        return view('gurunew.inbox', compact('documents'));
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Exports\ComplaintsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ComplaintController extends Controller
{
    // User: List complaints
    public function index()
    {
        $complaints = Complaint::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('complaints.index', compact('complaints'));
    }

    // User: Create form
    public function create()
    {
        return view('complaints.create');
    }

    // User: Store complaint
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10',
        ]);

        Complaint::create([
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        // Return JSON for AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Keluhan berhasil dikirim']);
        }

        return back()->with('success', 'Keluhan berhasil dikirim ke admin');
    }

    // User: Show detail
    public function show(Complaint $complaint)
    {
        if ($complaint->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        return view('complaints.show', compact('complaint'));
    }

    // Admin: List all complaints
    public function adminIndex()
    {
        $complaints = Complaint::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('admin.complaints.index', compact('complaints'));
    }

    // Admin: Export complaints to Excel
    public function export(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|in:1,7,30',
        ]);

        $days = (int) $request->days;
        $filename = 'keluhan_' . $days . '_hari_' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new ComplaintsExport($days), $filename);
    }
}

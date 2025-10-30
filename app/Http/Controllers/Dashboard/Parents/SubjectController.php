<?php

namespace App\Http\Controllers\Dashboard\Parents;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index(Request $request)
    // {
    //     $this->authorize('view subject');
    //     try {
    //         // ✅ Base query for active subjects
    //         $query = Subject::where('is_active', 'active');

    //         // ✅ If search keyword provided, filter by name
    //         if ($request->filled('search')) {
    //             $search = $request->input('search');
    //             $query->where('name', 'like', "%{$search}%");
    //         }

    //         // ✅ Get results
    //         $subjects = $query->get();
    //         $totalSubjects = $subjects->count();
    //         return view('dashboard.parents.subjects.index', compact('subjects', 'totalSubjects'));
    //     } catch (\Throwable $th) {
    //         Log::error('Subjects Index Failed', ['error' => $th->getMessage()]);
    //         return redirect()->back()->with('error', "Something went wrong! Please try again later");
    //         throw $th;
    //     }
    // }
    public function index(Request $request)
    {
        $this->authorize('view subject');

        try {
            // ✅ Base query for active subjects
            $query = Subject::where('is_active', 'active');

            // ✅ If search keyword provided, filter by name
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where('name', 'like', "%{$search}%");
            }

            // ✅ Get all active subjects
            $subjects = $query->get();
            $totalSubjects = $subjects->count();

            // ✅ Group subjects by base name (e.g. "Coding 5-8" → "Coding")
            $groupedSubjects = $subjects->groupBy(function ($item) {
                return preg_replace('/\s+\d+-\d+$/', '', $item->name);
            });

            // ✅ Pass grouped subjects to the view
            return view('dashboard.parents.subjects.index', [
                'subjects' => $groupedSubjects,
                'totalSubjects' => $totalSubjects,
            ]);
        } catch (\Throwable $th) {
            Log::error('Subjects Index Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
        }
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

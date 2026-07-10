<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Staff;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $staffs = Staff::where('admin_id', auth()->id())->get();
        return response()->json($staffs);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|email|unique:staffs,email',
            'role' => 'required|string|max:255',
            'workingdays' => 'required|array',
            'workingdays.*.day' => 'required|string',
            'workingdays.*.start' => 'required|string',
            'workingdays.*.end' => 'required|string',
        ]);

        $staff = Staff::create([
            'admin_id' => auth()->id(),
            'fname' => $validated['firstName'],
            'lname' => $validated['lastName'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'schedule' => $validated['workingdays'],
        ]);

        return response()->json($staff, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $staff = Staff::where('admin_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'firstName' => 'sometimes|string|max:255',
            'lastName' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:staffs,email,' . $staff->id,
            'role' => 'sometimes|string|max:255',
            'workingdays' => 'sometimes|array',
            'workingdays.*.day' => 'sometimes|string',
            'workingdays.*.start' => 'sometimes|string',
            'workingdays.*.end' => 'sometimes|string',
        ]);

        $map = [
            'firstName' => 'fname',
            'lastName' => 'lname',
            'email' => 'email',
            'role' => 'role',
            'workingdays' => 'schedule',
        ];

        foreach ($map as $inputKey => $dbKey) {
            if (array_key_exists($inputKey, $validated)) {
                $staff->$dbKey = $validated[$inputKey];
            }
        }

        $staff->save();

        return response()->json($staff);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

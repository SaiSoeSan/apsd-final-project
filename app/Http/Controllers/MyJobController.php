<?php

namespace App\Http\Controllers;

use App\Models\MyJob;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class MyJobController extends Controller
{
    // Display a listing of the resource.
    public function index()
    {
        $myjobs = MyJob::orderBy('created_at', 'desc')->get();
        return response()->json($myjobs);
    }

    // Store a newly created resource in storage.
    public function store(Request $request)
    {
        try {
            $request->validate([
                'company' => 'required|string|max:255',
                'job_title' => 'required|string|max:255',
                'applied_from' => 'required|string|max:255',
                'application_link' => 'required|string',
                'note' => 'nullable|string',
            ]);
            $myjob = MyJob::create($request->all());
            return response()->json([
                'message' => 'Job created successfully',
                'job' => $myjob,
            ], 201);
        }catch (QueryException $e) {
            if ($e->getCode() === '23000') { // Unique constraint violation
                return response()->json(['message' => 'A job with the same company and title already exists.'], 400);
            }
            return response()->json(['message' => 'Failed to create job'], 500);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Display the specified resource.
    public function show($id)
    {
        try {
            $myjob = MyJob::findOrFail($id);
            return response()->json([
                "job" => $myjob,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Job not found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Update the specified resource in storage.
    public function update(Request $request, $id)
    {
        try {
        $myjob = MyJob::findOrFail($id);
        $myjob->update($request->all());
        return response()->json([
            'message' => 'Job updated successfully',
            'job' => $myjob,
        ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Job not found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Remove the specified resource from storage.
    public function destroy($id)
    {

        try {
            $myjob = MyJob::findOrFail($id);
            $myjob->delete();
            return response()->json([
                'message' => 'Job deleted successfully',
            ],204);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) { 
            return response()->json([
                'message' => 'Job not found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

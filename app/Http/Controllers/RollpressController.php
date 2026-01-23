<?php

namespace App\Http\Controllers;

use App\Models\Rollpress;
use Illuminate\Http\Request;

class RollpressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('Rollpress.addpressorder');
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
    public function show(Rollpress $rollpress)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Rollpress $rollpress)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Rollpress $rollpress)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rollpress $rollpress)
    {
        //
    }
}

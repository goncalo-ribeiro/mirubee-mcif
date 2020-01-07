<?php

namespace App\Http\Controllers;

use App\ReadingThreePhase;
use Illuminate\Http\Request;

class ReadingThreePhaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //return Reading::all();
        return ReadingThreePhase::whereBetween('time', [1573570607, 1573572787])->get();
        //where('time', '<', 1 )->get();

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ReadingThreePhase  $reading
     * @return \Illuminate\Http\Response
     */
    public function show(ReadingThreePhase $reading)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ReadingThreePhase  $reading
     * @return \Illuminate\Http\Response
     */
    public function edit(ReadingThreePhase $reading)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ReadingThreePhase  $reading
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ReadingThreePhase $reading)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ReadingThreePhase  $reading
     * @return \Illuminate\Http\Response
     */
    public function destroy(ReadingThreePhase $reading)
    {
        //
    }
}

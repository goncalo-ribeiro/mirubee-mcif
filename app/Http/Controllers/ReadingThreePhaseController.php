<?php

namespace App\Http\Controllers;

use App\ReadingThreePhase;
use App\Device;
use Illuminate\Http\Request;

use App\Http\Resources\ReadingThreePhase as ReadingThreePhaseResource;

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
        $reading = new ReadingThreePhase;

        $reading->ip = $request->ip;
        $reading->time = $request->time;
        $reading->v1 = $request->v1;
        $reading->v2 = $request->v2;
        $reading->v3 = $request->v3;
        $reading->vt = $request->vt;
        $reading->i1 = $request->i1;
        $reading->i2 = $request->i2;
        $reading->i3 = $request->i3;
        $reading->it = $request->it;
        $reading->p1 = $request->p1;
        $reading->p2 = $request->p2;
        $reading->p3 = $request->p3;
        $reading->pt = $request->pt;
        $reading->a1 = $request->a1;
        $reading->a2 = $request->a2;
        $reading->a3 = $request->a3;
        $reading->at = $request->at;
        $reading->r1 = $request->r1;
        $reading->r2 = $request->r2;
        $reading->r3 = $request->r3;
        $reading->rt = $request->rt;
        $reading->q1 = $request->q1;
        $reading->q2 = $request->q2;
        $reading->q3 = $request->q3;
        $reading->qt = $request->qt;
        $reading->f1 = $request->f1;
        $reading->f2 = $request->f2;
        $reading->f3 = $request->f3;
        $reading->ft = $request->ft;
        $reading->e1 = $request->e1;
        $reading->e2 = $request->e2;
        $reading->e3 = $request->e3;
        $reading->et = $request->et;
        $reading->o1 = $request->o1;
        $reading->o2 = $request->o2;
        $reading->o3 = $request->o3;
        $reading->ot = $request->ot;
        $reading->ps = $request->ps;

        $reading->calc_time = 1;
        $reading->calc_day_week = strtolower(date('l', $request->time));
        $reading->calc_day_month = date('j', $request->time);
        $reading->calc_year = date('Y', $request->time);
        $reading->calc_month = date('m', $request->time);
        $reading->calc_hour = date('H', $request->time);
        $reading->calc_minute = date('i', $request->time);
        
        // $request->request->add(['calc_time' => 1]);
        // $request->request->add(['calc_day_week' => strtolower(date('l', $request->time))]);
        // $request->request->add(['calc_day_month' => date('j', $request->time)]);
        // $request->request->add(['calc_year' => date('Y', $request->time)]);
        // $request->request->add(['calc_month' => date('m', $request->time)]);
        // $request->request->add(['calc_hour' => date('H', $request->time)]);
        // $request->request->add(['calc_minute' => date('i', $request->time)]);

        $device = Device::where('mac_address', '=', $request->mac)->first();
    
        $reading->device()->associate($device);
        
        $reading->save();

        return response()->json(['message' => 'a new reading was created', 
        'reading' => new ReadingThreePhaseResource($reading)], 
        201);
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

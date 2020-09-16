<?php

namespace App\Http\Controllers;

use App\ReadingThreePhase;
use App\Device;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Auth;

use App\Http\Resources\ReadingThreePhase as ReadingThreePhaseResource;
use App\Http\Resources\Device as DeviceResource;

use App\Events\ReadingInserted;

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
        //Log::debug($request);
        $deviceCreated = false;

        if(is_null(Device::where('mac_address', $request->mac)->first())){
            $device = new Device;
            $device->type = 'three phase right';
            $device->mac_address = $request->mac;
            $device->model = $request->model;
            $device->soft = $request->soft;
            $device->user_id = Auth::user()->id;
            $device->product_id = 2;

            $device->save();
            $deviceCreated = true;
        }

        $reading = new ReadingThreePhase;

        $reading->ip = $request->ip;
        $reading->time = $request->time;
        $reading->v1 = encrypt($request->v1);
        $reading->v2 = encrypt($request->v2);
        $reading->v3 = encrypt($request->v3);
        $reading->vt = encrypt($request->vt);
        $reading->i1 = encrypt($request->i1);
        $reading->i2 = encrypt($request->i2);
        $reading->i3 = encrypt($request->i3);
        $reading->it = encrypt($request->it);
        $reading->p1 = encrypt($request->p1);
        $reading->p2 = encrypt($request->p2);
        $reading->p3 = encrypt($request->p3);
        $reading->pt = encrypt($request->pt);
        $reading->a1 = encrypt($request->a1);
        $reading->a2 = encrypt($request->a2);
        $reading->a3 = encrypt($request->a3);
        $reading->at = encrypt($request->at);
        $reading->r1 = encrypt($request->r1);
        $reading->r2 = encrypt($request->r2);
        $reading->r3 = encrypt($request->r3);
        $reading->rt = encrypt($request->rt);
        $reading->q1 = encrypt($request->q1);
        $reading->q2 = encrypt($request->q2);
        $reading->q3 = encrypt($request->q3);
        $reading->qt = encrypt($request->qt);
        $reading->f1 = encrypt($request->f1);
        $reading->f2 = encrypt($request->f2);
        $reading->f3 = encrypt($request->f3);
        $reading->ft = encrypt($request->ft);
        $reading->e1 = encrypt($request->e1);
        $reading->e2 = encrypt($request->e2);
        $reading->e3 = encrypt($request->e3);
        $reading->et = encrypt($request->et);
        $reading->o1 = encrypt($request->o1);
        $reading->o2 = encrypt($request->o2);
        $reading->o3 = encrypt($request->o3);
        $reading->ot = encrypt($request->ot);
        $reading->ps = encrypt($request->ps);

        $reading->calc_time = 1;
        $reading->calc_day_week = strtolower(date('l', $request->time));
        $reading->calc_day_month = date('j', $request->time);
        $reading->calc_year = date('Y', $request->time);
        $reading->calc_month = date('m', $request->time);
        $reading->calc_hour = date('H', $request->time);
        $reading->calc_minute = date('i', $request->time);

        if(!$deviceCreated){
            $device = Device::where('mac_address', '=', $request->mac)->first();
        }   
        $reading->device()->associate($device);
        $reading->save();

        event(new ReadingInserted($reading, Auth::user()));

        return response()->json(['message' => 'a new reading was created', 
        'reading' => new ReadingThreePhaseResource($reading),
        'deviceCreated' => new DeviceResource($device)], 
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

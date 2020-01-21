<?php

namespace App\Http\Controllers;

use App\Device;
use App\Site;
use Illuminate\Http\Request;

use App\Http\Resources\Device as DeviceResource;

use Illuminate\Support\Facades\Auth;

class DeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        return DeviceResource::collection($user->devices);
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
     * @param  \App\Device  $device
     * @return \Illuminate\Http\Response
     */
    public function show(Device $device)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Device  $device
     * @return \Illuminate\Http\Response
     */
    public function edit(Device $device)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Device  $device
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $device = Device::where('id', $request->id)->first();
        $name = $request->name;
        $site = $request->site;
        $user = Auth::user();
        
        if (is_null($device)){
            return response()->json(['errors' => ['id' => ["No device was found"]]], 400);
        }
        if ($user->id != $device->user->id){
            return response()->json(['errors' => ['user' => ["This device doesn't belong to the user"]]], 400);
        }

        if (is_null($name) || $name == ""){
            $device->name = $name;
        }
        else{
            if ($name != $device->name){
                $validation = $request->validate([
                    'name' => 'unique:devices',
                ]);
        
                $device->name = $name;
            }
        }
        
        if($site['id'] == -1){
            $device->site_id = null;
        }
        else{
            if(is_null($device->site) || $site['id'] != $device->site->id){
                $queriedSite = Site::where('id', $site['id'])->first();
                if(is_null($queriedSite)){
                    return response()->json(['errors' => ['site' => ["This site doesn't exist"]]], 400);
                }
                if( $queriedSite->user->id == $user->id){
                    $device->site_id = $site['id'];
                }else {
                    return response()->json(['errors' => ['user' => ["This site doesn't belong to the user"]]], 400);
                }
            }
        }
        $device->save();

        $device = Device::where('id', $request->id)->first();

        return response()->json(['message' => 'This device was updated', 'device' => new DeviceResource($device)], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Device  $device
     * @return \Illuminate\Http\Response
     */
    public function destroy(Device $device)
    {
        //
    }
}

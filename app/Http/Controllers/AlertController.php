<?php

namespace App\Http\Controllers;

use App\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Resources\Alert as AlertResource;

use App\Http\Requests\Alert as AlertRequest;

class AlertController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        return AlertResource::collection($user->alerts);
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
    public function store(AlertRequest $request)
    {
        $alert = new Alert;

        $request->validated();
        
        $alert->user()->associate(Auth::user());
        $alert->fill($request->all());
        $alert->save();

        return response()->json(['message' => 'a new alert was created', 'alert' => new AlertResource($alert)], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Alert  $alert
     * @return \Illuminate\Http\Response
     */
    public function show(Alert $alert)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Alert  $alert
     * @return \Illuminate\Http\Response
     */
    public function edit(Alert $alert)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Alert  $alert
     * @return \Illuminate\Http\Response
     */
    public function update(AlertRequest $request, Alert $alert)
    {
        $alert = Alert::where('id', $request->id)->first();
        $user = Auth::user();

        if (is_null($alert)){
            return response()->json(['errors' => ['id' => ["No alert was found"]]], 400);
        }
        if ($user->id != $alert->user->id){
            $temp = (object) ['user' => ["This site doesn't belong to the user"]];
            return response()->json(['errors' => $temp], 400);
        }

        $request->validated();
        $alert->fill($request->all());
        $alert->save();

        return response()->json(['message' => 'the alert was successfully updated', 'alert' => new AlertResource($alert)], 201);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Alert  $alert
     * @return \Illuminate\Http\Response
     */
    public function destroy($alertId)
    {
        $alert = Alert::where('id', $alertId)->first();
        $user = Auth::user();

        if (is_null($alert)){
            return response()->json(['errors' => ['id' => ["No alert was found"]]], 400);
        }
        if ($user->id != $alert->user->id){
            return response()->json(['errors' => ['user' => ["This alert doesn't belong to the user"]]], 400);
        }
        Alert::destroy($alertId);
        return response()->json(['message' => 'the selected alert was deleted', 'alert' => new AlertResource($alert), 'id' =>$alertId], 200);
    }

    public function readNotifications($alertId){
        $user = Auth::user();

        foreach ($user->unreadNotifications as $notification) {
            if($notification->data['alert_id'] == $alertId){
                $notification->markAsRead();
            }
        }
    }

    public function deleteNotification($notificationId){
        $user = Auth::user();

        foreach ($user->Notifications as $notification) {
            if($notification->id == $notificationId){
                $notification->delete();
            }
        }
        return response()->json(['message' => 'the notification was deleted'], 200);
    }

    public function deleteAlertNotifications($alertId){
        $user = Auth::user();
        $counter = 0;

        foreach ($user->Notifications as $notification) {
            if($notification->data['alert_id'] == $alertId){
                $notification->delete();
                $counter++;
            }
        }
        if($counter == 1){
            return response()->json(['message' => '1 notification was deleted'], 200);
        }
        if($counter > 1){
            return response()->json(['message' => $counter . ' notifications were deleted'], 200);
        }
    }
}

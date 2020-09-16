<?php

namespace App\Http\Controllers;

use App\Site;
use App\ReadingThreePhase;
use App\Device;
use App\Tariff;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use App\Http\Requests\Tariff as TariffRequest;

use App\Http\Resources\Site as SiteResource;
use App\Http\Resources\Device as DeviceResource;
use App\Http\Resources\Tariff as TariffResource;
use App\Http\Resources\ReadingThreePhase as ReadingThreePhaseResource;

class SiteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //abort(403, 'Unauthorized action.');
        $user = Auth::user();
        $sites = $user->sites;

        /* o site resource ja faz isto
        foreach ($sites as &$site) {
            if(!is_null($site->location)){
                $site->location = decrypt($site->location);
            }
        }
*/
        return SiteResource::collection($sites);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $site = new Site;
        $name = $request->name;
        $location = encrypt($request->location);
        $user = Auth::user();

        $validation = $request->validate([
            'name' => 'required | unique:sites',
        ]);

        $site->name = $name;
        $site->location = $location;

        $site->user()->associate(Auth::user());
        $site->save();

        return response()->json(['message' => 'a new site was created', 'site' => new SiteResource($site)], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Site  $site
     * @return \Illuminate\Http\Response
     */
    public function show(Site $site)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Site  $site
     * @return \Illuminate\Http\Response
     */
    public function edit(Site $site)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Site  $site
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Site $site)
    {
        $site = Site::where('id', $request->id)->first();
        $name = $request->name;
        $location = encrypt($request->location);
        $user = Auth::user();
        
        if (is_null($site)){
            $temp = (object) ['id' => ["No site was found"]];
            return response()->json(['errors' => ['id' => ["No site was found"]]], 400);
        }
        if ($user->id != $site->user->id){
            $temp = (object) ['user' => ["This site doesn't belong to the user"]];
            return response()->json(['errors' => $temp], 400);
        }

        $validation = $request->validate([
            'name' => 'required | unique:sites',
        ]);

        $site->name = $name;
        $site->location = $location;

        $site->save();

        return response()->json(['message' => 'This site was updated', 'site' => new SiteResource($site)], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Site  $site
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $site = Site::where('id', $request->id)->first();
        $user = Auth::user();

        if (is_null($site)){
            $temp = (object) ['id' => ["No site was found"]];
            return response()->json(['errors' => $temp], 400);
        }
        if ($user->id != $site->user->id){
            $temp = (object) ['user' => ["This site doesn't belong to the user"]];
            return response()->json(['errors' => $temp], 400);
        }
        Site::destroy($request->id);
        return response()->json(['message' => 'the selected site was deleted', 'site' => new SiteResource($site)], 200);
    }

    public function getReadings(Request $request, $siteId, $start, $end){

        if(($end - $start) >  3456000){
            $errorObject = (object) ['time' => ["The range of dates is too big"]];
            return response()->json(['errors' => $errorObject], 400);
        }

        $site = Site::find($siteId);
        $user = Auth::user();

        if($site->user->id != $user->id){
            return response()->json(['errors' => ['user' => ["this site doesn't belong to the user"]]], 400);
        }

        $readings = $site->readingsThreePhase->whereBetween('time', [$start, $end])->sortBy('time')->values();
        //Log::debug($readings);

        return response()->json(['message' => 'readings successfully retrieved', 'readings' => ReadingThreePhaseResource::collection($readings), 'params' => [$start, $end]], 200);
        
        //dd($siteId, $start, $end);
    }

    public function getSiteDevices(Request $request, $siteId){
        $user = Auth::user();
        $site = Site::where('id', $siteId)->first();

        if($site->user->id != $user->id){
            return response()->json(['errors' => ['user' => ["this site doesn't belong to the user"]]], 400);
        }

        
        $devices = $site->devices;
        return response()->json(['message' => 'user devices successfully retrieved', 'devices' => $devices], 200);
    }    

    public function setTariff(TariffRequest $request, $siteId){
        $user = Auth::user();
        $site = Site::where('id', $siteId)->first();

        if($site->user->id != $user->id){
            return response()->json(['errors' => ['user' => ["this site doesn't belong to the user"]]], 400);
        }

        if(!is_null($site->tariff)){
            return response()->json(['errors' => ['site' => ["this site already has a tariff associated"]]], 400);
        }

        $tariff = new Tariff;

        Log::debug($request);

        $request->validated();

        $tariff->fill($request->all());

        $tariff->save();

        $site->tariff()->associate($tariff);
        $site->save();

        return response()->json(['message' => 'tariff set up successfully', 'tariff' => new TariffResource($tariff)], 200);
    }

    public function updateTariff(TariffRequest $request, $siteId){
        $user = Auth::user();
        $site = Site::where('id', $siteId)->first();

        if($site->user->id != $user->id){
            return response()->json(['errors' => ['user' => ["this site doesn't belong to the user"]]], 400);
        }

        Log::debug($request);

        $tariff = $site->tariff;

        $request->validated();

        $tariff->fill($request->all());

        $tariff->save();

        $site->tariff()->associate($tariff);
        $site->save();

        return response()->json(['message' => 'tariff updated successfully', 'tariff' => new TariffResource($tariff)], 200);
    }
    

    public function deleteTariff($siteId){
        $user = Auth::user();
        $site = Site::where('id', $siteId)->first();

        if($site->user->id != $user->id){
            return response()->json(['errors' => ['user' => ["this site doesn't belong to the user"]]], 400);
        }

        $tariff = $site->tariff;

        if(is_null($tariff)){
            return response()->json(['errors' => ['site' => ["this site doesn't have an associated tariff"]]], 400);
        }

        $tariff->delete();

        return response()->json(['message' => 'tariff erased successfully', 'site' => new SiteResource($site)], 200);
    }
}

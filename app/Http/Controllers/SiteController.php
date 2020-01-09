<?php

namespace App\Http\Controllers;

use App\Site;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use App\Http\Resources\Site as SiteResource;

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

        return SiteResource::collection($user->sites);
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
        $name = $request->siteName;
        $location = $request->siteLocation;
        $user = Auth::user();

        if (is_null($name) || $name == ""){
            return response()->json(['msg' => "the site's name can't be empty"], 400);
        }
        $site->name = $name;
        if(is_null($location)){
            $site->location = $location;
        }

        $site->user()->associate(Auth::user());

        //$user->sites()->associate($site);

        $site->save();

        return response()->json(['msg' => 'a new site was created', 'site' => new SiteResource($site)], 201);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Site  $site
     * @return \Illuminate\Http\Response
     */
    public function destroy(Site $site)
    {
        //
    }
}

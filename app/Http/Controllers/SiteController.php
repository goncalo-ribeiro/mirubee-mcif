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
        $name = $request->name;
        $location = $request->location;
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
        $location = $request->location;
        $user = Auth::user();
        
        if (is_null($site)){
            $temp = (object) ['id' => ["No site was found"]];
            return response()->json(['errors' => $temp], 400);
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
}

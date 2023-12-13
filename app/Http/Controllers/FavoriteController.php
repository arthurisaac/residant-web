<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FavoriteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $properties = Property::query()
            ->with('Pictures')
            ->with('User')
            ->withCount(['Favorite as favorite' => function ($query) {
                $query->where('user', '=', auth()->user()->id);
            }])
            ->whereHas('Favorite', function ($query) {
                $query->where('user', '=', auth()->user()->id);
            })
            ->get();
        return response()->json([
            'data' => $properties,
        ]);
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
        $validator = Validator::make($request->all(), [
            'property' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = new Favorite([
            'user' => auth()->user()->id,
            'property' => $request->get('property'),
        ]);
        $data->save();

        return response()->json([
            'message' => 'Favorite saved.',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Favorite $favorite)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Favorite $favorite)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Favorite $favorite)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'property' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        Favorite::query()
            ->where("user", auth()->user()->id ?? null)
            ->where("property", $request->get("property"))
            ->delete();

        return response()->json([
            'message' => 'Deleted.',
            'user' => auth()->user()->id ?? null,
            'property' => $request->get("property"),
        ]);
    }
}

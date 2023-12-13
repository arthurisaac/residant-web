<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyPicture;
use Illuminate\Http\Request;

class PropertyPictureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $request->validate([
            'property' => 'required',
            'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg',
        ]);

        $path = $request->file('image') ? $request->file('image')->store('properties', 'public') : null;

        $data = new PropertyPicture([
            'image' => $path,
            'property' => $request->get("property")
        ]);
        $data->save();

        return response()->json([
            'message' => 'Image saved.',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(PropertyPicture $propertyPicture)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PropertyPicture $propertyPicture)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PropertyPicture $propertyPicture)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = PropertyPicture::query()->find($id);
        if ($data) {
            $data->delete();
        }
        return response()->json(["message" => "Supprimé avec succès"]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyPicture;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PropertyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only('store','delete','show', 'update');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //$token = $request->bearerToken();
        $user = Auth::guard('sanctum')->user();

        $prixMin = $request->get('prix-min');
        $prixMax = $request->get('prix-max');
        $nombreChambre = $request->get('nombre-chambre');
        $nombreParking = $request->get('nombre-garage');

        $properties = Property::query()
            ->with('Pictures')
            ->with('User')
            ->withCount(['Favorite as favorite' => function ($query) {
                $query->where('user', '=', $user->id ?? null);
            }])
            ->get();

        if ($prixMin || $prixMax || $nombreChambre || $nombreParking) {
            $properties = Property::query()
                ->with('Pictures')
                ->with('User')
                ->where('nombre_chambre', $nombreChambre)
                ->where('nombre_parking', $nombreParking)
                ->whereBetween('prix', [$prixMin, $prixMax])
                ->withCount(['Favorite as favorite' => function ($query) {
                    $query->where('user', '=', $user->id ?? null);
                }])
                ->get();
        }


        return response()->json([
            'data' => $properties,
            'user' => $user,
        ]);
    }

    public function myProperties(Request $request)
    {
        //$token = $request->bearerToken();
        $user = Auth::guard('sanctum')->user();

        $prixMin = $request->get('prix-min');
        $prixMax = $request->get('prix-max');
        $nombreChambre = $request->get('nombre-chambre');
        $nombreParking = $request->get('nombre-garage');

        $properties = Property::query()
            ->with('Pictures')
            ->with('User')
            ->withCount(['Favorite as favorite' => function ($query) {
                $query->where('user', '=', $user->id ?? null);
            }])
            ->where("user", $user->id)
            ->get();

        if ($prixMin || $prixMax || $nombreChambre || $nombreParking) {
            $properties = Property::query()
                ->with('Pictures')
                ->with('User')
                ->where('nombre_chambre', $nombreChambre)
                ->where('nombre_parking', $nombreParking)
                ->where('user', $user->id)
                ->whereBetween('prix', [$prixMin, $prixMax])
                ->withCount(['Favorite as favorite' => function ($query) {
                    $query->where('user', '=', $user->id ?? null);
                }])
                ->get();
        }


        return response()->json([
            'data' => $properties,
            'user' => $user,
        ]);
    }

    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'q' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $properties = Property::query()
            ->with('Pictures')
            ->with('User')
            ->withCount(['Favorite as favorite' => function ($query) {
                $query->where('user', '=', auth()->user()->id ?? null);
            }])
            ->when($request->q,
                function (Builder $builder) use ($request) {
                    $builder->where('nom', 'like', "%{$request->q}%")
                        ->orWhere('type_propriete', 'like', "%{$request->q}%")
                        ->orWhere('ville', 'like', "%{$request->q}%")
                        ->orWhere('quartier', 'like', "%{$request->q}%")
                        ->orWhere('prix', 'like', "%{$request->q}%");
                })
            ->get();


        return response()->json([
            'data' => $properties,
            'user' => auth()->user()->id ?? null,
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
        $request->validate([
            'nom' => 'required',
            'total_pictures' => 'required|numeric',
            'image_principale' => 'required|image|mimes:jpg,png,jpeg,gif,svg',
        ]);

        $path = $request->file('image_principale') ? $request->file('image_principale')->store('properties', 'public') : null;
        $data = new Property([
            'user' => auth()->user()->id,
            'nom' => $request->get('nom'),
            'quartier' => $request->get('quartier'),
            'ville' => $request->get('ville'),
            'pays' => $request->get('pays'),
            'latitude' => $request->get('latitude'),
            'longitude' => $request->get('longitude'),
            'description' => $request->get('description'),
            'image_principale' => $path,
            'type_propriete' => $request->get('type_propriete'),
            'superficie' => $request->get('superficie') ?? 0,
            'superficie_unite' => $request->get('superficie_unite'),
            'nombre_chambre' => $request->get('nombre_chambre') ?? 0,
            'nombre_bain' => $request->get('nombre_bain') ?? 0,
            'nombre_parking' => $request->get('nombre_parking') ?? 0,
            'nombre_personne_max' => $request->get('nombre_personne_max') ?? 1,
            'meublee' => $request->get('meublee') == true ? 1 : 0,
            'prix' => $request->get('prix') ?? 0,
            'type_prix' => $request->get('type_prix'),
            'reduction' => $request->get('reduction') ?? 0,
            'reduction_nombre_reservation' => $request->get('reduction_nombre_reservation') ?? 0,
        ]);
        $data->save();

        for ($i = 0; $i < $request->get('total_pictures'); $i++) {
            if ($request->file("picture_$i")) {
                $path = $request->file("picture_$i") ? $request->file("picture_$i")->store('pictures', 'public') : null;

                $data = new PropertyPicture([
                    'image' => $path,
                    'property' => $data->id,
                ]);
                $data->save();
            }
        }

        return response()->json([
            'message' => 'Property saved.',
            'property' => $data,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Property $property)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Property $property)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
    }

    public function updateProperty(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required',
            'total_pictures' => 'required|numeric',
            'image_principale' => 'image|mimes:jpg,png,jpeg,gif,svg',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = Property::query()->find($id);
        if ($data) {
            if( $request->file('image_principale')) {
                $data->image_principale = $request->file('image_principale')->store('properties', 'public');
            }
            $data->nom = $request->get('nom');
            $data->quartier = $request->get('quartier');
            $data->ville = $request->get('ville');
            $data->pays = $request->get('pays');
            $data->latitude = $request->get('latitude');
            $data->longitude = $request->get('longitude');
            $data->description = $request->get('description');
            $data->type_propriete = $request->get('type_propriete');
            $data->superficie = $request->get('superficie') ?? 0;
            $data->superficie_unite = $request->get('superficie_unite');
            $data->nombre_chambre = $request->get('nombre_chambre') ?? 0;
            $data->nombre_bain = $request->get('nombre_bain') ?? 0;
            $data->nombre_parking = $request->get('nombre_parking') ?? 0;
            $data->nombre_personne_max = $request->get('nombre_personne_max') ?? 1;
            $data->meublee = $request->get('meublee') == true ? 1 : 0;
            $data->prix = $request->get('prix') ?? 0;
            $data->type_prix = $request->get('type_prix');
            $data->reduction = $request->get('reduction') ?? 0;
            $data->reduction_nombre_reservation = $request->get('reduction_nombre_reservation') ?? 0;
            $data->save();
        }

        for ($i = 0; $i < $request->get('total_pictures'); $i++) {
            if ($request->file("picture_$i")) {
                $path = $request->file("picture_$i") ? $request->file("picture_$i")->store('pictures', 'public') : null;

                $data = new PropertyPicture([
                    'image' => $path,
                    'property' => $data->id,
                ]);
                $data->save();
            }
        }

        return response()->json([
            'message' => 'Property saved.',
            'property' => $data,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = Property::query()->find($id);
        if ($data) {
            $data->delete();
        }
        return response()->json(["message", "Supprimé avec succès"]);

    }
}

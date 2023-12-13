<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('phone', 'password');

        if (auth()->attempt($credentials)) {
            //$token = auth()->user()->createToken('API Token')->accessToken;
            $token = auth()->user()->createToken('SECUREKEY')->plainTextToken;

            return response()->json(['token' => $token, 'user' => auth()->user()]);
        }

        return response()->json(['error' => 'Unauthorized', 'message' => 'Numéro de téléphone ou mot de passe incorrecte'], 401);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users',
            'email' => 'string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'name' => $request->nom . " " . $request->nom,
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('SECUREKEY')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully.',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function detail() {
        return response()->json(['user' => auth()->user()]);
    }

    public function change_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Les données fournies étaient invalides.' . $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::query()
            ->find(auth()->user()->id)
            ->update(['password' => Hash::make($request->get('password'))]);

        if ($user) {
            //$token = \auth()->user()->createToken('SAUVIEALERTETOKENKEY')->plainTextToken;
            return response()->json([
                'message' => 'Mot de passe modifié avec succès.',
                'user' => $user,
            ], 201);
        }

        return response()->json(['error' => 'Unauthorized', 'message' => 'Non authorisé'], 401);

    }

    public function change_profile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'phone' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Les données fournies étaient invalides.' . $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::query()
            ->find(auth()->user()->id)
            ->update([
                'name' => $request->get("nom") . " " . $request->get("prenom"),
                'nom' => $request->get('nom'),
                'prenom' => $request->get('prenom'),
                'phone' => $request->get('phone'),
                'email' => $request->get('email'),
            ]);

        if ($user) {
            return response()->json([
                'message' => 'Informations personnelles modifiés avec succès.',
                'user' => \auth()->user(),
            ], 201);
        }

        return response()->json(['error' => 'Unauthorized', 'message' => 'Non authorisé'], 401);

    }

    /*public function deleteUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::query()->find($request->get("user"));

        if ($user) {
            $userDeleted = new UserDeleted([
                'id_user' => $user->id,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'country' => $user->country,
                'countryCode' => $user->countryCode,
                'active' => $user->active,
                'password' => $user->password,
            ]);
            $userDeleted->save();

            $user->delete();
        }

        return response()->json([
            'message' => 'User deleted successfully.',
        ], 201);
    }*/

}

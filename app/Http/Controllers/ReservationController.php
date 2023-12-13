<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $reservations = Reservation::query()
            ->with("Property")
            ->where("user", auth()->user()->id)
            ->get();
        return response()->json([
            'data' => $reservations,
        ]);
    }

    public function myReservations(Request $request)
    {
        $reservations = Reservation::query()
            ->with("Property")
            ->with("User")
            ->whereHas('Property', function(Builder $query) {
                $query->where("user", auth()->user()->id);
            })
            ->get();
        return response()->json([
            'data' => $reservations,
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
            'phone' => 'required',
            'date_debut' => 'required|date_format:Y-m-d H:i',
            'date_fin' => 'required|date_format:Y-m-d H:i',
            'amount' => 'required|numeric',
            'otp' => 'required|numeric',
            'method' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Les données fournies étaient invalides.' . $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $amount = (int)$request->get('amount');
        $ext_txn_id = time();
        $otp = $request->get('otp');
        $phone_number = $request->get('phone');

        $xml = "<?xml version='1.0' encoding='UTF-8'?>
                <COMMAND>
                <TYPE>OMPREQ</TYPE>
                <customer_msisdn>$phone_number</customer_msisdn>
                <merchant_msisdn>76688276</merchant_msisdn>
                <api_username>YGGTEST</api_username>
                <api_password>Orange@123</api_password>
                <amount>$amount</amount>
                <PROVIDER>101</PROVIDER>
                <PROVIDER2>101</PROVIDER2>
                <PAYID>12</PAYID>
                <PAYID2>12</PAYID2>
                <otp>$otp</otp>
                <reference_number>789233</reference_number>
                <ext_txn_id>201500068544</ext_txn_id>
                </COMMAND>";

        $url = 'https://testom.orange.bf:9008/payment';
        $send_context = stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-Type: application/xml',
                'content' => $xml
            )
        ));
        /*$response = file_get_contents($url, false, $send_context);

        $xml = simplexml_load_string('<response>' . $response . '</response>') or die('Error: Cannot create object');

        if ($xml->status == 200) {
            $payment = new OrderPayment([
                'order' => $request->get('order'),
                'user' => $request->get('user'),
                'amount' => $request->get('amount'),
                'payment_method' => $request->get('payment_method'),
                'phone_number' => $request->get('phone_number'),
                'opt_code' => $request->get('opt_code'),
            ]);
            $payment->save();

            return response()->json([
                'message' => 'Payment saved successfully.',
            ], 201);
        } else {
            return response()->json([
                'message' => 'Une erreur dans le paiement.',
                'data' => $xml
            ]);
        }*/

        $payment = new Reservation([
            "user" => auth()->user()->id,
            "property" => $request->get("property"),
            "date_debut" => $request->get("date_debut"),
            "date_fin" => $request->get("date_fin"),
            "method" => $request->get("method"),
            "amount" => $request->get("amount"),
            "phone" => $request->get("phone"),
            "otp" => $request->get("otp"),
            "status" => 0,
        ]);
        $payment->save();

        return response()->json([
            'message' => 'Payment saved successfully.',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Reservation $reservation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reservation $reservation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reservation $reservation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reservation $reservation)
    {
        //
    }

    public function availableData(Request $request) {
        $validator = Validator::make($request->all(), [
            'property' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Les données fournies étaient invalides.' . $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $reservations = Reservation::query()
            ->where("property", $request->get("property"))
            ->whereDate("date_debut", ">", Carbon::now())
            ->get();
        $dates = [];
        foreach ($reservations as $reservation) {
            //var_dump($this->datesEntreDeuxDates($reservation->date_debut, $reservation->date_fin));
            array_push($dates, ...$this->datesEntreDeuxDates($reservation->date_debut, $reservation->date_fin));
            //array_merge($dates, $this->datesEntreDeuxDates($reservation->date_debut, $reservation->date_debut));
        }
        //dd($this->datesEntreDeuxDates("2023-12-07 12:00:00" , "2023-12-11 12:00:00"));
        //$dt = array_unique($dates);

        return response()->json([
            'message' => 'Dates indisponibles',
            'data' => $dates,
        ]);
    }

    function datesEntreDeuxDates($dateDebut, $dateFin) {
        $listeDates = [];

        $dateActuelle = Carbon::parse($dateDebut);

        while ($dateActuelle <= Carbon::parse($dateFin)) {
            $listeDates[] = array("date" => $dateActuelle->toDateTimeString());
            $dateActuelle->addDay();
        }

        return $listeDates;
    }
}

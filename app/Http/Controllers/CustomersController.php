<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Resources\CustomersResource;
use App\Jobs\PropertiesProcess;
use Illuminate\Support\Facades\Validator;

class CustomersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return CustomersResource::collection(Customers::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:customers',
            'client_id' => 'required|string|unique:customers',
            'client_secret' => 'required|string|unique:customers'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 403);
        }
        $customers = Customers::create([
            'email' => $request->email,
            'client_id' => $request->client_id,
            'client_secret' => $request->client_secret,
            'platform' => $request->platform,
            'platform_credentials' => $request->platform_credentials,
            'status' => $request->status,
        ]);

        return new CustomersResource($customers);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (empty($id)) {
            return response()->json(['error' => 'Customer id not found'], 403);
        }
        return CustomersResource::collection(Customers::where('id', $id)->get());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (empty($id)) {
            return response()->json(['error' => 'Customer id not found'], 403);
        }
        $validator = Validator::make($request->all(), [
            'client_id' => 'nullable|string|unique:customers',
            'client_secret' => 'nullable|string|unique:customers'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 403);
        }
        $customers = Customers::where('id', $id)
            ->update([
                'client_id' => $request->client_id,
                'client_secret' => $request->client_secret,
                'status' => $request->status,
            ]);

        return new CustomersResource(Customers::where('id', $id)->get());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

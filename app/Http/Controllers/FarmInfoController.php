<?php

namespace App\Http\Controllers;

use App\Models\FarmSetting;
use Illuminate\Http\Request;

class FarmInfoController extends Controller
{
    public function index()
    {
        $farm = [
            'name'    => FarmSetting::get('farm_name', "Escalona's Farm") ?: "Escalona's Farm",
            'address' => FarmSetting::get('farm_address', 'Pagkakaisa, Naujan, Or. Mindoro') ?: 'Pagkakaisa, Naujan, Or. Mindoro',
            'phone'   => FarmSetting::get('farm_phone', '+63 917 123 4567') ?: '+63 917 123 4567',
            'email'   => FarmSetting::get('farm_email', 'escalona.farm@gmail.com') ?: 'escalona.farm@gmail.com',
        ];

        return view('farm-info', compact('farm'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'farm_name'    => 'required|string|max:255',
            'farm_address' => 'required|string|max:255',
            'farm_phone'   => 'required|string|max:50',
            'farm_email'   => 'required|email|max:255',
        ]);

        FarmSetting::set('farm_name',    $request->farm_name);
        FarmSetting::set('farm_address', $request->farm_address);
        FarmSetting::set('farm_phone',   $request->farm_phone);
        FarmSetting::set('farm_email',   $request->farm_email);

        return back()->with('success', 'Farm information updated successfully!');
    }
}
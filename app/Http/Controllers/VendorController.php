<?php

namespace App\Http\Controllers;

use App\Enums\VendorStatusEnum;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VendorController extends Controller
{
    function profile(Vendor $vendor)  {


    }
    function store(Request $request) {
        $user = $request->user();
        $request->validate([
            'store_name' => ['required', 'regex:/^[a-z0-9-]+$/', Rule::unique('vendors', 'store_name')->ignore($user->id, 'user_id')],
            'store_address' => 'nullable',

        ],[
            'store_name.regex'=> 'Store name can only contain lowercase letters, numbers, and hyphens.',
        ]);

        $vendor = $user->vendor?: new Vendor();
        $vendor->user_id = $user->id;
        $vendor->status=VendorStatusEnum::Approved;
        $vendor->store_name = $request->store_name;
        $vendor->store_address = $request->store_address;
        $vendor->save();
        $user->assignRole('Vendor');
        $user->save();
        // dd($user->isStripeAccountActive());

        // return redirect()->route('profile.update')->with('success', 'Vendor application submitted successfully!');

    }
}

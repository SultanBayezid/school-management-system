<?php

namespace App\Http\Controllers;

use App\Models\SchoolSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function edit()
    {
        $settings = SchoolSetting::firstOrCreate(
            ['id' => 1],
            ['school_name' => 'My School'],
        );

        return view('settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $settings = SchoolSetting::firstOrCreate(
            ['id' => 1],
            ['school_name' => 'My School'],
        );

        $data = $request->validate([
            'school_name'      => ['required', 'string', 'max:150'],
            'school_address'   => ['nullable', 'string', 'max:300'],
            'phone'            => ['nullable', 'string', 'max:40'],
            'email'            => ['nullable', 'email', 'max:150'],
            'website'          => ['nullable', 'url', 'max:200'],
            'principal_name'   => ['nullable', 'string', 'max:150'],
            'established_year' => ['nullable', 'integer', 'min:1800', 'max:'.now()->year],
        ]);

        $settings->update($data);

        return back()->with('success', 'School settings saved.');
    }
}

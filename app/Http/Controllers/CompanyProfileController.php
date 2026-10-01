<?php

namespace App\Http\Controllers;

use App\Models\CompanyProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyProfileController extends Controller
{
    public function edit(): View
    {
        return view('admin.company-profile', ['profile' => CompanyProfile::first() ?? new CompanyProfile()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'branch_name' => ['nullable', 'string', 'max:255'],
            'branch_title' => ['nullable', 'string', 'max:255'],
            'invoice_print_type' => ['required', 'in:a4,pos'],
            'branch_address' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'invoice_header' => ['nullable', 'image', 'max:2048'],
            'invoice_footer' => ['nullable', 'image', 'max:2048'],
        ]);

        $profile = CompanyProfile::first() ?? new CompanyProfile();

        foreach (['logo', 'invoice_header', 'invoice_footer'] as $field) {
            if ($request->hasFile($field)) {
                $validated[$field] = $request->file($field)->store('company', 'public');
            } else {
                unset($validated[$field]);
            }
        }

        $profile->fill($validated)->save();

        return redirect()->route('company-profile.edit')->with('success', 'Company profile updated successfully.');
    }
}

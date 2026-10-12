<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;

class MaintenanceController extends Controller
{
    public function show(): View
    {
        return view('dashboard.activation', [
            'active' => app()->isDownForMaintenance(),
        ]);
    }

    public function toggle(Request $request): RedirectResponse
    {
        if (app()->isDownForMaintenance()) {
            Artisan::call('up');

            $status = 'Maintenance mode is now off — the site is live for everyone.';
        } else {
            Artisan::call('down', [
                '--render' => 'errors.503',
                '--retry' => 60,
            ]);

            $status = 'Maintenance mode is now on — visitors will see the maintenance page.';
        }

        return redirect()->route('maintenance.show')->with('status', $status);
    }

    public function preview(): View
    {
        return view('errors.503');
    }
}

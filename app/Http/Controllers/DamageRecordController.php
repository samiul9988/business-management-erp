<?php

namespace App\Http\Controllers;

use App\Models\Damage;
use Illuminate\View\View;

class DamageRecordController extends Controller
{
    public function index(): View
    {
        return view('pending.damage-record', ['damages' => Damage::with('user')->latest('damage_date')->latest('id')->get()]);
    }
}

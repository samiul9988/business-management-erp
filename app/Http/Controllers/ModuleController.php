<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ModuleController extends Controller
{
    /**
     * Maps a dashboard module card to the sidebar menu section (by its
     * 'text') it should list, so both stay in sync automatically.
     */
    private const MODULES = [
        'sales' => ['label' => 'Sales Module', 'menu' => 'Sales Module', 'accent' => '#2377ed'],
        'purchase' => ['label' => 'Purchase Module', 'menu' => 'Purchase Module', 'accent' => '#3eb27c'],
        'pending' => ['label' => 'Pending Module', 'menu' => 'Pending Module', 'accent' => '#ff851e'],
        'repair' => ['label' => 'Repair Module', 'menu' => 'Repair Module', 'accent' => '#ed4d70'],
        'warranty' => ['label' => 'Warranty Module', 'menu' => 'Warranty Module', 'accent' => '#ff851e'],
        'accounts' => ['label' => 'Accounts Module', 'menu' => 'Accounts Module', 'accent' => '#7752df'],
        'hr-payroll' => ['label' => 'HR & Payroll', 'menu' => 'HR & Payroll', 'accent' => '#7752df'],
        'reports' => ['label' => 'Reports Module', 'menu' => 'Reports Module', 'accent' => '#27aaa3'],
        'administration' => ['label' => 'Administration', 'menu' => 'Administration', 'accent' => '#607697'],
    ];

    public function show(string $slug): View
    {
        if (! isset(self::MODULES[$slug])) {
            throw new NotFoundHttpException();
        }

        $module = self::MODULES[$slug];

        $section = collect(config('adminlte.menu'))
            ->first(fn ($item) => ($item['text'] ?? null) === $module['menu']);

        return view('modules.show', [
            'title' => $module['label'],
            'accent' => $module['accent'],
            'items' => $section['submenu'] ?? [],
        ]);
    }
}

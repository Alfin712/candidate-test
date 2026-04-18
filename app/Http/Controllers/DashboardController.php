<?php

namespace App\Http\Controllers;

use App\Models\Layer;
use App\Models\Layup;
use App\Models\Supplier;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalSuppliers = Supplier::count();
        $activeSuppliers = Supplier::where('status', 'Active')->count();
        $inactiveSuppliers = $totalSuppliers - $activeSuppliers;
        $totalLayups = Layup::count();
        $totalLayers = Layer::count();

        $recentLayups = Layup::with('supplier')
            ->orderByDesc('updated_at')
            ->limit(6)
            ->get();

        $recentSuppliers = Supplier::orderByDesc('updated_at')
            ->limit(4)
            ->get();

        return view('manager.dashboard', compact(
            'totalSuppliers',
            'activeSuppliers',
            'inactiveSuppliers',
            'totalLayups',
            'totalLayers',
            'recentLayups',
            'recentSuppliers',
        ));
    }
}

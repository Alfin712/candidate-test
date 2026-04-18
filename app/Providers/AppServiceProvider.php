<?php

namespace App\Providers;

use App\Contracts\LayerRepositoryInterface;
use App\Contracts\LayupRepositoryInterface;
use App\Contracts\SupplierExportServiceInterface;
use App\Contracts\SupplierImportServiceInterface;
use App\Contracts\SupplierRepositoryInterface;
use App\Models\Layer;
use App\Models\Layup;
use App\Models\Supplier;
use App\Policies\LayerPolicy;
use App\Policies\LayupPolicy;
use App\Policies\SupplierPolicy;
use App\Repositories\LayerRepository;
use App\Repositories\LayupRepository;
use App\Repositories\SupplierRepository;
use App\Services\SupplierExportService;
use App\Services\SupplierImportService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public array $bindings = [
        SupplierExportServiceInterface::class => SupplierExportService::class,
        SupplierImportServiceInterface::class => SupplierImportService::class,
        SupplierRepositoryInterface::class    => SupplierRepository::class,
        LayupRepositoryInterface::class       => LayupRepository::class,
        LayerRepositoryInterface::class       => LayerRepository::class,
    ];

    public function register(): void
    {
    }

    public function boot(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        Gate::policy(Supplier::class, SupplierPolicy::class);
        Gate::policy(Layup::class, LayupPolicy::class);
        Gate::policy(Layer::class, LayerPolicy::class);
    }
}

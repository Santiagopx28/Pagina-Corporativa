<?php

namespace App\Providers;

use App\Models\Categoria;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class ViewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.custom');
        View::composer('layouts.app', function ($view) {
            $sidebarCategorias = Categoria::activo()
                ->withCount(['documentos as documentos_activos_count' => function ($query) {
                    $query->activo();
                }])
                ->orderBy('orden')
                ->get();

            $view->with('sidebarCategorias', $sidebarCategorias);
        });
    }
}
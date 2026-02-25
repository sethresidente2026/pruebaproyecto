<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Eloquent\ActividadRepository;
use App\Repositories\Contracts\ActividadRepositoryInterface;
use App\Repositories\Contracts\EspacioRepositoryInterface;
use App\Repositories\Eloquent\EspacioRepository;
use App\Repositories\Contracts\DocenteRepositoryInterface;
use App\Repositories\Eloquent\DocenteRepository;
use App\Repositories\Contracts\AsistenciaRepositoryInterface;
use App\Repositories\Eloquent\AsistenciaRepository;
use App\Repositories\Contracts\HorarioRepositoryInterface;
use App\Repositories\Eloquent\HorarioRepository;
use App\Repositories\Contracts\GrupoRepositoryInterface;
use App\Repositories\Eloquent\GrupoRepository;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind
        (ActividadRepositoryInterface::class,
        ActividadRepository::class
        );
        $this->app->bind(
            EspacioRepositoryInterface::class,
            EspacioRepository::class
        );
        $this->app->bind(
            DocenteRepositoryInterface::class,
            DocenteRepository::class
        );
        $this->app->bind(
            AsistenciaRepositoryInterface::class,
            AsistenciaRepository::class
        );
        $this->app->bind(
            HorarioRepositoryInterface::class,
            HorarioRepository::class
            );
            $this->app->bind(
                GrupoRepositoryInterface::class,
                GrupoRepository::class
                );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

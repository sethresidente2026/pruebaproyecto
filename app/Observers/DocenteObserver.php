<?php

namespace App\Observers;

use App\Models\Docente;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Exception;
class DocenteObserver
{
    /**
     * Handle the Docente "created" event.
     */
    public function created(Docente $docente): void
    {
        //
    }

    /**
     * Handle the Docente "updated" event.
     */
    public function updated(Docente $docente): void
    {
       if( $docente->isDirty('estatus')){
        $viejoestatus=$docente->getOriginal('estatus');
        $nuevoestatus=$docente->estatus;
        $usuarioId = Auth::id() ?? 'Sistema/Consola';

            Log::info("AUDITORÍA UGM - Cambio de Estatus", [
                'docente_id' => $docente->id,
                'nombre'     => $docente->nombre,
                'de'         => $viejoestatus,
                'a'          => $nuevoestatus,
                'realizado_por_usuario_id' => $usuarioId,
                'fecha'      => now()->toDateTimeString()
            ]);
       }
       
    }
    

    /**
     * Handle the Docente "deleted" event.
     */
    public function deleted(Docente $docente): void
    {
        //
    }

    /**
     * Handle the Docente "restored" event.
     */
    public function restored(Docente $docente): void
    {
        //
    }

    /**
     * Handle the Docente "force deleted" event.
     */
    public function forceDeleted(Docente $docente): void
    {
        //
    }
}

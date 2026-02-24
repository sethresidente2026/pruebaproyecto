<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Académico UGM</title>
    <style>
        /* Estilos estrictos para impresión PDF */
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #333; }
        
        /* Cabecera Institucional (Posicionamiento para Logo y Título) */
        .header { 
            text-align: center; 
            border-bottom: 2px solid #D1101A; 
            padding-bottom: 15px; 
            margin-bottom: 20px; 
            position: relative; /* Clave para el logo absoluto */
            min-height: 70px;
        }
        
        /* El Logo en la esquina izquierda */
        .logo-pdf {
            position: absolute;
            top: 0;
            left: 0;
            width: 85px; /* Tamaño del logo */
        }

        /* Área central para que el título no estorbe con el logo */
        .title-area {
            margin-left: 90px;
            margin-right: 90px;
        }

        .title { font-size: 20px; font-weight: bold; color: #2C3E50; margin: 0; letter-spacing: 1px; }
        .subtitle { font-size: 14px; color: #7f8c8d; margin: 5px 0 10px 0; }
        .badge-rojo { background-color: #D1101A; color: white; padding: 4px 10px; font-weight: bold; font-size: 12px; border-radius: 4px; }
        
        /* Datos del Documento */
        .meta-info { width: 100%; margin-bottom: 20px; font-size: 10px; }
        .meta-info td { border: none; padding: 2px; }
        .text-right { text-align: right; }

        /* Títulos de Sección */
        .section-title { background-color: #2C3E50; color: #ffffff; padding: 6px 10px; font-size: 13px; font-weight: bold; margin-top: 25px; margin-bottom: 10px; text-transform: uppercase; }

        /* Tablas */
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th { background-color: #f8f9fa; border-bottom: 2px solid #D1101A; padding: 8px; text-align: left; font-weight: bold; font-size: 10px; color: #2C3E50; text-transform: uppercase; }
        td { border-bottom: 1px solid #eeeeee; padding: 8px; font-size: 10px; }
        tr:nth-child(even) { background-color: #fdfdfd; }

        /* Utilidades */
        .page-break { page-break-after: always; }
        
        /* Pie de página con contador automático */
        .footer { position: fixed; bottom: -30px; left: 0px; right: 0px; height: 50px; font-size: 9px; color: #95a5a6; border-top: 1px solid #eeeeee; padding-top: 10px; }
        .page-number:after { content: counter(page); }
    </style>
</head>
<body>

    <div class="footer">
        <table style="width: 100%; border:none; margin: 0;">
            <tr>
                <td style="border:none; padding:0;">Sistema de Gestión Académica - UGM Rectoría Centro</td>
                <td style="border:none; padding:0; text-align:right;">Página <span class="page-number"></span></td>
            </tr>
        </table>
    </div>

    <div class="header">
        @if($logo)
            <img src="{{ $logo }}" class="logo-pdf">
        @endif
        
        <div class="title-area">
            <h1 class="title">UNIVERSIDAD DEL GOLFO DE MÉXICO</h1>
            <h2 class="subtitle">Rectoría Centro</h2>
            <span class="badge-rojo">REPORTE DE OPERACIÓN ACADÉMICA</span>
        </div>
    </div>

    <table class="meta-info">
        <tr>
            <td><strong>Generado por:</strong> Sistema de Gestión</td>
            <td class="text-right"><strong>Fecha de emisión:</strong> {{ $fecha }}</td>
        </tr>
    </table>

    <div class="section-title">1. Resumen Ejecutivo</div>
    <table>
        <thead>
            <tr>
                <th style="text-align:center;">Docentes Activos</th>
                <th style="text-align:center;">Grupos Formados</th>
                <th style="text-align:center;">Horarios Asignados</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align:center; font-size: 14px; font-weight:bold;">{{ count($docentes) }}</td>
                <td style="text-align:center; font-size: 14px; font-weight:bold;">{{ count($grupos) }}</td>
                <td style="text-align:center; font-size: 14px; font-weight:bold;">{{ count($horarios) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">2. Agenda de Horarios Asignados</div>
    <table>
        <thead>
            <tr>
                <th>Día</th>
                <th>Horario</th>
                <th>Grupo (Actividad)</th>
                <th>Docente Asignado</th>
                <th>Espacio</th>
            </tr>
        </thead>
        <tbody>
            @forelse($horarios as $horario)
            <tr>
                <td><strong>{{ $horario->dia_semana }}</strong></td>
                <td>{{ substr($horario->hora_inicio, 0, 5) }} - {{ substr($horario->hora_fin, 0, 5) }}</td>
                <td>{{ $horario->grupo->nombre ?? 'N/A' }}</td>
                <td>{{ $horario->grupo->docente->nombre ?? '' }} {{ $horario->grupo->docente->apellidos ?? '' }}</td>
                <td>{{ $horario->espacio->nombre ?? 'Sin asignar' }}</td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center;">No hay horarios registrados.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="page-break"></div> 

    <div class="section-title">3. Catálogo de Grupos</div>
    <table>
        <thead>
            <tr>
                <th>Grupo</th>
                <th>Nivel / Ciclo</th>
                <th>Cupo</th>
                <th>Docente</th>
            </tr>
        </thead>
        <tbody>
            @forelse($grupos as $grupo)
            <tr>
                <td><strong>{{ $grupo->nombre }}</strong> <br> <span style="font-size: 9px; color: #7f8c8d;">({{ $grupo->actividad->nombre ?? 'N/A' }})</span></td>
                
                <td>{{ $grupo->nivelEducativo->nombre ?? 'N/A' }} <br> <span style="font-size: 9px; color: #7f8c8d;">{{ $grupo->ciclo->nombre ?? 'N/A' }}</span></td>
                
                <td>{{ $grupo->cupo_maximo }} lugares</td>
                <td>{{ $grupo->docente->nombre ?? '' }} {{ $grupo->docente->apellidos ?? 'Sin asignar' }}</td>
            </tr>
            @empty
            <tr><td colspan="4" style="text-align:center;">No hay grupos registrados.</td></tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
<?php

namespace App\Http\Controllers;

use App\Services\MercadoPublico\Settings\EtlSettings;

use CodeInc\StripAccents\StripAccents;
use Illuminate\Http\Request;


class AdminConfiguracionesController extends Controller 
{
    public function index(EtlSettings $etlSettings) 
    {
        return view('configuraciones', [
            'settings' => [
                'filtro_tipo_licitacion' => StripAccents::strip($etlSettings->filtro_tipo_licitacion),
                'filtro_region_comprador' => StripAccents::strip($etlSettings->filtro_region_comprador),
                'filtro_palabras_excluidas' => StripAccents::strip($etlSettings->filtro_palabras_excluidas),
                'filtro_palabras_incluidas' => StripAccents::strip($etlSettings->filtro_palabras_incluidas),
                'filtro_palabras_clave_educacion' => StripAccents::strip($etlSettings->filtro_palabras_clave_educacion),
                'filtro_palabras_clave_industria_creativa' => StripAccents::strip($etlSettings->filtro_palabras_clave_industria_creativa),
                'filtro_palabras_clave_turismo' => StripAccents::strip($etlSettings->filtro_palabras_clave_turismo),
                'filtro_palabras_excluidas_educacion_y_cultura' => StripAccents::strip($etlSettings->filtro_palabras_excluidas_educacion_y_cultura),
                'filtro_palabras_clave_espacio_publico' => StripAccents::strip($etlSettings->filtro_palabras_clave_espacio_publico),
                'filtro_palabras_clave_diseño' => StripAccents::strip($etlSettings->filtro_palabras_clave_diseño),
                'filtro_palabras_clave_vialidad' => StripAccents::strip($etlSettings->filtro_palabras_clave_vialidad),
                'filtro_palabras_clave_obras_publicas' => StripAccents::strip($etlSettings->filtro_palabras_clave_obras_publicas),
                'filtro_palabras_clave_salud' => StripAccents::strip($etlSettings->filtro_palabras_clave_salud),
                'filtro_palabras_clave_inclusion' => StripAccents::strip($etlSettings->filtro_palabras_clave_inclusion),
                'filtro_palabras_excluidas_desarrollo_social' => StripAccents::strip($etlSettings->filtro_palabras_excluidas_desarrollo_social),
                'filtro_palabras_clave_agua' => StripAccents::strip($etlSettings->filtro_palabras_clave_agua),
                'filtro_palabras_clave_apr' => StripAccents::strip($etlSettings->filtro_palabras_clave_apr),
                'filtro_palabras_clave_sistemas_alimentarios' => StripAccents::strip($etlSettings->filtro_palabras_clave_sistemas_alimentarios),
                'filtro_palabras_clave_produccion_sostenible' => StripAccents::strip($etlSettings->filtro_palabras_clave_produccion_sostenible),
                'filtro_palabras_clave_eficiencia_energetica' => StripAccents::strip($etlSettings->filtro_palabras_clave_eficiencia_energetica),
                'filtro_palabras_clave_climatizacion' => StripAccents::strip($etlSettings->filtro_palabras_clave_climatizacion),
                'filtro_palabras_excluidas_medio_ambiente' => StripAccents::strip($etlSettings->filtro_palabras_excluidas_medio_ambiente),
            ]
        ]);
    }

    public function store(Request $request, EtlSettings $etlSettings) 
    {
        $newSettings = $request->input('settings');

        foreach($newSettings as $newSettingName => $newSettingValue) {
            $newSettingName = StripAccents::strip(mb_convert_encoding($newSettingName, 'UTF-8'));
            $etlSettings->$newSettingName = strtolower($newSettingValue);
        }

        $etlSettings->save();
        return redirect()->back()->with('message', 'Configuraciones guardadas');
    }
}

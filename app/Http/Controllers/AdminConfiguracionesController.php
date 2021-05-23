<?php

namespace App\Http\Controllers;

use App\Services\MercadoPublico\Settings\EtlSettings;

use Illuminate\Http\Request;

class AdminConfiguracionesController extends Controller 
{
    public function index(EtlSettings $etlSettings) 
    {
        return view('configuraciones', [
            'settings' => [
                'filtro_tipo_licitacion' => $etlSettings->filtro_tipo_licitacion,
                'filtro_palabras_excluidas' => $etlSettings->filtro_palabras_excluidas,
                'filtro_palabras_incluidas' => $etlSettings->filtro_palabras_incluidas,
                'filtro_palabras_clave_educacion' => $etlSettings->filtro_palabras_clave_educacion,
                'filtro_palabras_clave_industria_creativa' => $etlSettings->filtro_palabras_clave_industria_creativa,
                'filtro_palabras_clave_turismo' => $etlSettings->filtro_palabras_clave_turismo,
                'filtro_palabras_excluidas_educacion_y_cultura' => $etlSettings->filtro_palabras_excluidas_educacion_y_cultura,
                'filtro_palabras_clave_espacio_publico' => $etlSettings->filtro_palabras_clave_espacio_publico,
                'filtro_palabras_clave_diseño' => $etlSettings->filtro_palabras_clave_diseño,
                'filtro_palabras_clave_vialidad' => $etlSettings->filtro_palabras_clave_vialidad,
                'filtro_palabras_clave_obras_publicas' => $etlSettings->filtro_palabras_clave_obras_publicas,
                'filtro_palabras_clave_salud' => $etlSettings->filtro_palabras_clave_salud,
                'filtro_palabras_clave_inclusion' => $etlSettings->filtro_palabras_clave_inclusion,
                'filtro_palabras_excluidas_desarrollo_social' => $etlSettings->filtro_palabras_excluidas_desarrollo_social,
                'filtro_palabras_clave_agua' => $etlSettings->filtro_palabras_clave_agua,
                'filtro_palabras_clave_apr' => $etlSettings->filtro_palabras_clave_apr,
                'filtro_palabras_clave_sistemas_alimentarios' => $etlSettings->filtro_palabras_clave_sistemas_alimentarios,
                'filtro_palabras_clave_produccion_sostenible' => $etlSettings->filtro_palabras_clave_produccion_sostenible,
                'filtro_palabras_clave_eficiencia_energetica' => $etlSettings->filtro_palabras_clave_eficiencia_energetica,
                'filtro_palabras_excluidas_medio_ambiente' => $etlSettings->filtro_palabras_excluidas_medio_ambiente,
            ]
        ]);
    }

    public function store(Request $request, EtlSettings $etlSettings) 
    {
        $newSettings = $request->input('settings');

        foreach($newSettings as $newSettingName => $newSettingValue) {
            $newSettingName = mb_convert_encoding($newSettingName, 'UTF-8');
            $etlSettings->$newSettingName = strtolower($newSettingValue);
        }

        $etlSettings->save();
        return redirect()->back()->with('message', 'Configuraciones guardadas');
    }
}

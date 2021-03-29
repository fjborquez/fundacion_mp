<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreatePalabrasClavesCategoriasSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.mercado_publico_filtro_palabras_clave_educacion', 'ee;e.e.;ji;j.i.;sc;sacu;vtf;escuela;colegio;reposición;mantenimiento;conservación;restauración;electrica;regularización;talleres;capacitación;daem;slep;servicio local;universidad;aula;aprendizaje;instituto;cft;comunidad educativa;liceo;sala cuna;educacion;esc.;junji;jardin infantil;fines educativos;facultad;investigación;material pedagogico;educativo;conectividad;internet;equipos computacionales;tablet');
        $this->migrator->add('general.mercado_publico_filtro_palabras_clave_industria_creativa', 'conservación;diseño;restauración;museo;producción;biblioteca;cultura;cultural;patrimonio;teatro;arte;parque nacional;audiovisual;arquitectura;motion;capsulas;pabellon;literario;histórica;libreria;palacio;monumento;inmaterial;eventos;audiencias;inmueble;radio;canal televisión;festival;feria;cine;catastro;editorial;bibliografía;libro;obras de arte;danza;difusión;espectáculo;música');
        $this->migrator->add('general.mercado_publico_filtro_palabras_clave_turismo', 'vacaciones;turismo;tour operador;agencias;familiar;gastronomia;rural;turistico;tur.;mice;transforma;sustentable;plan de acción;plan de desarrollo;aventura;montaña;enoturismo;ecoturismo');
    
        $this->migrator->add('general.mercado_publico_filtro_palabras_excluidas_educacion_y_cultura', 'servicios;guardias;seguridad;vigilancia;autos;vehículos;motos;camiones;buses;arriendos;aseo;juguetes;caja alimentos;mercadería');

        $this->migrator->add('general.mercado_publico_filtro_palabras_clave_espacio_publico', 'sede;cancha;multicancha;estadio;ciclovía;plaza;plazoleta;parque;skatepark;gimnasio;senderos');
        $this->migrator->add('general.mercado_publico_filtro_palabras_clave_diseño', 'diseño;ingeniería;pladeco;estudio prefactibilidad;plan;desarrollo');
        $this->migrator->add('general.mercado_publico_filtro_palabras_clave_vialidad', 'semaforos;caminos;calles;rutas;pavimentación;pavimento;vereda;paraderos;aguas lluvias;alcantarillado');
        $this->migrator->add('general.mercado_publico_filtro_palabras_clave_obras_publicas', 'puentes;rampas;muelles caleta;fiscalía;en general mejoramientos a edficiós públicos;mejoramiento de infraestructura;cierres perimetral;centro de salud;hospital;cesfam;box');
        $this->migrator->add('general.mercado_publico_filtro_palabras_clave_salud', 'insumos;equipo;equipamiento;ambulancia;protesis;colchones antiescaras');
        $this->migrator->add('general.mercado_publico_filtro_palabras_clave_inclusion', 'fundación;audifonos;lentes;sillas de rueda');

        $this->migrator->add('general.mercado_publico_filtro_palabras_excluidas_desarrollo_social', 'servicios;administración;consultas;remedios;servicios de exámenes;medicamentos;insumos de aseo;cajas de alimento;canastas familiares;insumos de oficina;farmacos;juguetes;mercadería;motos.');
        
        $this->migrator->add('general.mercado_publico_filtro_palabras_clave_agua', 'aguas grises;tratamiento de agua;aguas servidas');
        $this->migrator->add('general.mercado_publico_filtro_palabras_clave_apr', 'agua potable;apr;regadío');
        $this->migrator->add('general.mercado_publico_filtro_palabras_clave_sistemas_alimentarios', 'indap;agricultura');
        $this->migrator->add('general.mercado_publico_filtro_palabras_clave_produccion_sostenible', 'reciclaje;residuos');
        $this->migrator->add('general.mercado_publico_filtro_palabras_clave_eficiencia_energetica', 'climatización;eficiencia energética;energía;led;solar;térmica');

        $this->migrator->add('general.mercado_publico_filtro_palabras_excluidas_medio_ambiente', 'correo electronicos;internet;telefonía movil;medicamentos;mg;ml;fármaco;exámenes;diagnostico;diálisis;radioterapia;servicios;remedios;sueros;materiales ferretería;guardias;seguridad;vigilancia;arriendos;juguetes;caja alimentos;mercadería;guantes;protectores;mascarillas;camión aljibe;disposición final');

    }
}

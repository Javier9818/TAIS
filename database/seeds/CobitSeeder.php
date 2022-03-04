<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CobitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('metas_empresariales')->insert([
            ['sigla' => 'EG01', 'perspectiva_fk' => 1, 'nombre' => 'Portafolio de productos y servicios competitivos'],
            ['sigla' => 'EG02', 'perspectiva_fk' => 1, 'nombre' => 'Gestión de riesgo del negocio'],
            ['sigla' => 'EG03', 'perspectiva_fk' => 1, 'nombre' => 'Cumplimiento de leyes y regulaciones externas'],
            ['sigla' => 'EG04', 'perspectiva_fk' => 1, 'nombre' => 'Calidad de la información financiera'],
            ['sigla' => 'EG05', 'perspectiva_fk' => 2, 'nombre' => 'Cultura de servicio orientada al cliente'],
            ['sigla' => 'EG06', 'perspectiva_fk' => 2, 'nombre' => 'Continuidad y disponibilidad del servicio del negocio'],
            ['sigla' => 'EG07', 'perspectiva_fk' => 2, 'nombre' => 'Calidad de la información de gestión'],
            ['sigla' => 'EG08', 'perspectiva_fk' => 3, 'nombre' => 'Optimización de costes de los procesos del negocio'],
            ['sigla' => 'EG09', 'perspectiva_fk' => 3, 'nombre' => 'Optimización de la funcionalidad de los procesos internos del negocio'],
            ['sigla' => 'EG10', 'perspectiva_fk' => 3, 'nombre' => 'Habilidades, motivación y productividad del personal'],
            ['sigla' => 'EG11', 'perspectiva_fk' => 3, 'nombre' => 'Cumplimiento con las políticas internass'],
            ['sigla' => 'EG12', 'perspectiva_fk' => 4, 'nombre' => 'Gestión de programas de transformación digital'],
            ['sigla' => 'EG13', 'perspectiva_fk' => 4, 'nombre' => 'Innovación de producto y negocio'],
        ]);

        DB::table('metas_alineamiento')->insert([
            ['sigla' => 'AG01', 'nombre' => 'Cumplimiento y soporte de I&T para el cumplimiento empresarial con las leyes y regulaciones externas'],
            ['sigla' => 'AG02', 'nombre' => 'Gestión de riesgo relacionado con I&T'],
            ['sigla' => 'AG03', 'nombre' => 'Beneficios obtenidos de el portafolio de inversiones y servicios relacionados con I&T'],
            ['sigla' => 'AG04', 'nombre' => 'Calidad de la información financiera relacionada con la tecnología'],
            ['sigla' => 'AG05', 'nombre' => 'Prestación de servicios I&T conforme a los requerimientos del negocio'],
            ['sigla' => 'AG06', 'nombre' => 'Agilidad para convertir los requerimientos del negocio en soluciones operativas'],
            ['sigla' => 'AG07', 'nombre' => 'Seguridad de la información, infraestructura de procesamiento y aplicaciones, y privacidad'],
            ['sigla' => 'AG08', 'nombre' => 'Habilitar y dar soporte a procesos de negocio mediante la integración de aplicaciones y tecnología'],
            ['sigla' => 'AG09', 'nombre' => 'Ejecución de programas dentro del plazo, sin exceder el presupuesto, y cumpliendo con los requisitos'],
            ['sigla' => 'AG10', 'nombre' => 'Calidad de la información sobre gestión de I&T'],
            ['sigla' => 'AG11', 'nombre' => 'Cumplimiento de I&T con las políticas internas'],
            ['sigla' => 'AG12', 'nombre' => 'Personal competente y motivado con un entendimiento mutuo de la tecnología y el negocio'],
            ['sigla' => 'AG13', 'nombre' => 'Conocimiento, experiencia e iniciativas para la innovación empresarial'],
        ]);

        DB::table('objetivos_control')->insert([
            ['sigla' => 'EDM01', 'nombre' => 'Asegurar el establecimiento y el mantenimiento del marco de gobierno'],
            ['sigla' => 'EDM02', 'nombre' => 'Asegurar la entrega de beneficios'],
            ['sigla' => 'EDM03', 'nombre' => 'Asegurar la optimización del riesgo'],
            ['sigla' => 'EDM04', 'nombre' => 'Asegurar la optimización de recursos'],
            ['sigla' => 'EDM05', 'nombre' => 'Asegurar el compromiso de las partes interesadas'],
            ['sigla' => 'APO01', 'nombre' => 'Gestionar el marco de gestión de TI'],
            ['sigla' => 'APO02', 'nombre' => 'Gestionar la estrategia'],
            ['sigla' => 'APO03', 'nombre' => 'Gestionar la arquitectura empresarial'],
            ['sigla' => 'APO04', 'nombre' => 'Gestionar la innovación'],
            ['sigla' => 'APO05', 'nombre' => 'Gestionar el portafolio'],
            ['sigla' => 'APO06', 'nombre' => 'Gestionar el presupuesto y los costes'],
            ['sigla' => 'APO07', 'nombre' => 'Gestionar los recursos humanos'],
            ['sigla' => 'APO08', 'nombre' => 'Gestionar las relaciones'],
            ['sigla' => 'APO09', 'nombre' => 'Gestionar los acuerdos de servicio'],
            ['sigla' => 'APO10', 'nombre' => 'Gestionar los proveedores'],
            ['sigla' => 'APO11', 'nombre' => 'Gestionar la calidad'],
            ['sigla' => 'APO12', 'nombre' => 'Gestionar los riesgos'],
            ['sigla' => 'APO13', 'nombre' => 'Gestionar la seguridad'],
            ['sigla' => 'APO14', 'nombre' => 'Gestionar los datos'],
            ['sigla' => 'BAI01', 'nombre' => 'Gestionar los programas'],
            ['sigla' => 'BAI02', 'nombre' => 'Gestionar la definición de requisitos'],
            ['sigla' => 'BAI03', 'nombre' => 'Gestionar la identificación y construcción de soluciones'],
            ['sigla' => 'BAI04', 'nombre' => 'Gestionar la disponibilidad y capacidad'],
            ['sigla' => 'BAI05', 'nombre' => 'Gestionar el cambio organizativo'],
            ['sigla' => 'BAI06', 'nombre' => 'Gestionar los cambios de TI'],
            ['sigla' => 'BAI07', 'nombre' => 'Gestionar la aceptación y la transición del cambio de TI'],
            ['sigla' => 'BAI08', 'nombre' => 'Gestionar el conocimiento'],
            ['sigla' => 'BAI09', 'nombre' => 'Gestionar los activos'],
            ['sigla' => 'BAI10', 'nombre' => 'Gestionar la configuración'],
            ['sigla' => 'BAI11', 'nombre' => 'Gestionar los proyectos'],
            ['sigla' => 'DSS01', 'nombre' => 'Gestionar las operaciones'],
            ['sigla' => 'DSS02', 'nombre' => 'Gestionar las peticiones y los incidentes del servicio'],
            ['sigla' => 'DSS03', 'nombre' => 'Gestionar los problemas'],
            ['sigla' => 'DSS04', 'nombre' => 'Gestionar la continuidad'],
            ['sigla' => 'DSS05', 'nombre' => 'Gestionar los servicios de seguridad'],
            ['sigla' => 'DSS06', 'nombre' => 'Gestionar los controles del proceso de negocio'],
            ['sigla' => 'MEA01', 'nombre' => 'Gestionar la monitorización del rendimiento y la conformidad'],
            ['sigla' => 'MEA02', 'nombre' => 'Gestionar el sistema de control interno'],
            ['sigla' => 'MEA03', 'nombre' => 'Gestionar el cumplimiento de los requisitos externos'],
            ['sigla' => 'MEA04', 'nombre' => 'Gestionar el aseguramiento'],
        ]);

        

    }
}

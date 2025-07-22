<?php

namespace Database\Seeders;

use App\Domain\Estudiante\Models\Estudiante;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EstudiantesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $estudiantes = [
            [
                'id' => 1,
                'nombrePrimario' => 'Luis',
                'nombreSecundario' => 'Miguel',
                'apellidoPrimario' => 'Perez',
                'apellidoSecundario' => 'Gonzalez',
                'cedula' => '8-987-654',
                'correo' => 'luis.perez@academia.pa',
            ],
            [
                'id' => 2,
                'nombrePrimario' => 'Ana',
                'nombreSecundario' => 'Sofia',
                'apellidoPrimario' => 'Rodriguez',
                'apellidoSecundario' => 'Martinez',
                'cedula' => '4-321-987',
                'correo' => 'ana.rodriguez@academia.pa',
            ],
            [
                'id' => 3,
                'nombrePrimario' => 'Carlos',
                'nombreSecundario' => 'Andres',
                'apellidoPrimario' => 'Sanchez',
                'apellidoSecundario' => 'Lopez',
                'cedula' => '9-654-321',
                'correo' => 'carlos.sanchez@academia.pa',
            ],
            [
                'id' => 4,
                'nombrePrimario' => 'Maria',
                'nombreSecundario' => 'Fernanda',
                'apellidoPrimario' => 'Garcia',
                'apellidoSecundario' => 'Fernandez',
                'cedula' => '3-111-222',
                'correo' => 'maria.garcia@academia.pa',
            ],
            [
                'id' => 5,
                'nombrePrimario' => 'Jose',
                'nombreSecundario' => 'David',
                'apellidoPrimario' => 'Diaz',
                'apellidoSecundario' => 'Ruiz',
                'cedula' => '10-333-444',
                'correo' => 'jose.diaz@academia.pa',
            ],
            [
                'id' => 6,
                'nombrePrimario' => 'Laura',
                'nombreSecundario' => 'Valentina',
                'apellidoPrimario' => 'Alvarez',
                'apellidoSecundario' => 'Moreno',
                'cedula' => '2-555-666',
                'correo' => 'laura.alvarez@academia.pa',
            ],
            [
                'id' => 7,
                'nombrePrimario' => 'Jorge',
                'nombreSecundario' => 'Alejandro',
                'apellidoPrimario' => 'Jimenez',
                'apellidoSecundario' => 'Hernandez',
                'cedula' => '6-777-888',
                'correo' => 'jorge.jimenez@academia.pa',
            ],
            [
                'id' => 8,
                'nombrePrimario' => 'Sofia',
                'nombreSecundario' => 'Camila',
                'apellidoPrimario' => 'Vazquez',
                'apellidoSecundario' => 'Gomez',
                'cedula' => '12-999-000',
                'correo' => 'sofia.vazquez@academia.pa',
            ],
            [
                'id' => 9,
                'nombrePrimario' => 'Miguel',
                'nombreSecundario' => 'Eduardo',
                'apellidoPrimario' => 'Navarro',
                'apellidoSecundario' => 'Torres',
                'cedula' => '1-246-810',
                'correo' => 'miguel.navarro@academia.pa',
            ],
            [
                'id' => 10,
                'nombrePrimario' => 'Elena',
                'nombreSecundario' => 'Gabriela',
                'apellidoPrimario' => 'Ramos',
                'apellidoSecundario' => 'Castro',
                'cedula' => '13-135-791',
                'correo' => 'elena.ramos@academia.pa',
            ],
            [
                'id' => 11,
                'nombrePrimario' => 'David',
                'nombreSecundario' => 'Antonio',
                'apellidoPrimario' => 'Iglesias',
                'apellidoSecundario' => 'Ortega',
                'cedula' => '5-369-258',
                'correo' => 'david.iglesias@academia.pa',
            ],
            [
                'id' => 12,
                'nombrePrimario' => 'Isabel',
                'nombreSecundario' => 'Mariana',
                'apellidoPrimario' => 'Serrano',
                'apellidoSecundario' => 'Guerrero',
                'cedula' => '7-147-258',
                'correo' => 'isabel.serrano@academia.pa',
            ],
            [
                'id' => 13,
                'nombrePrimario' => 'Javier',
                'nombreSecundario' => 'Alberto',
                'apellidoPrimario' => 'Molina',
                'apellidoSecundario' => 'Cano',
                'cedula' => '11-369-147',
                'correo' => 'javier.molina@academia.pa',
            ],
            [
                'id' => 14,
                'nombrePrimario' => 'Carmen',
                'nombreSecundario' => 'Lucia',
                'apellidoPrimario' => 'Garrido',
                'apellidoSecundario' => 'Santos',
                'cedula' => '8-258-369',
                'correo' => 'carmen.garrido@academia.pa',
            ],
            [
                'id' => 15,
                'nombrePrimario' => 'Adrian',
                'nombreSecundario' => 'Esteban',
                'apellidoPrimario' => 'Castillo',
                'apellidoSecundario' => 'Leon',
                'cedula' => '4-147-369',
                'correo' => 'adrian.castillo@academia.pa',
            ],
            [
                'id' => 16,
                'nombrePrimario' => 'Raquel',
                'nombreSecundario' => 'Victoria',
                'apellidoPrimario' => 'Cruz',
                'apellidoSecundario' => 'Prieto',
                'cedula' => '9-369-258',
                'correo' => 'raquel.cruz@academia.pa',
            ],
            [
                'id' => 17,
                'nombrePrimario' => 'Fernando',
                'nombreSecundario' => 'Daniel',
                'apellidoPrimario' => 'Pascual',
                'apellidoSecundario' => 'Gallego',
                'cedula' => '3-258-147',
                'correo' => 'fernando.pascual@academia.pa',
            ],
            [
                'id' => 18,
                'nombrePrimario' => 'Beatriz',
                'nombreSecundario' => 'Adriana',
                'apellidoPrimario' => 'Blanco',
                'apellidoSecundario' => 'Sanz',
                'cedula' => '10-147-258',
                'correo' => 'beatriz.blanco@academia.pa',
            ],
            [
                'id' => 19,
                'nombrePrimario' => 'Ricardo',
                'nombreSecundario' => 'Javier',
                'apellidoPrimario' => 'Marin',
                'apellidoSecundario' => 'Calvo',
                'cedula' => '2-369-147',
                'correo' => 'ricardo.marin@academia.pa',
            ],
            [
                'id' => 20,
                'nombrePrimario' => 'Veronica',
                'nombreSecundario' => 'Andrea',
                'apellidoPrimario' => 'Vega',
                'apellidoSecundario' => 'Perez',
                'cedula' => '6-258-369',
                'correo' => 'veronica.vega@academia.pa',
            ],
        ];

        foreach ($estudiantes as $estudiante) {
            Estudiante::create([
                'id' => $estudiante['id'],
                'nombrePrimario' => $estudiante['nombrePrimario'],
                'nombreSecundario' => $estudiante['nombreSecundario'],
                'apellidoPrimario' => $estudiante['apellidoPrimario'],
                'apellidoSecundario' => $estudiante['apellidoSecundario'],
                'cedula' => $estudiante['cedula'],
                'correo' => $estudiante['correo'],
                'contrasena' => Hash::make('students123'),
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Domain\Curso\Models\Profesor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProfesoresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $profesores = [
            [
                'id' => 1,
                'nombrePrimario' => 'Roberto',
                'nombreSecundario' => 'Carlos',
                'apellidoPrimario' => 'Perez',
                'apellidoSecundario' => 'Gomez',
                'cedula' => '8-123-456',
                'correo' => 'roberto.perez@academia.pa',
            ],
            [
                'id' => 2,
                'nombrePrimario' => 'Maria',
                'nombreSecundario' => 'Luisa',
                'apellidoPrimario' => 'Gonzalez',
                'apellidoSecundario' => 'Rodriguez',
                'cedula' => '4-789-123',
                'correo' => 'maria.gonzalez@academia.pa',
            ],
            [
                'id' => 3,
                'nombrePrimario' => 'Juan',
                'nombreSecundario' => 'Jose',
                'apellidoPrimario' => 'Martinez',
                'apellidoSecundario' => 'Sanchez',
                'cedula' => '9-456-789',
                'correo' => 'juan.martinez@academia.pa',
            ],
            [
                'id' => 4,
                'nombrePrimario' => 'Ana',
                'nombreSecundario' => 'Isabel',
                'apellidoPrimario' => 'Lopez',
                'apellidoSecundario' => 'Garcia',
                'cedula' => '3-987-654',
                'correo' => 'ana.lopez@academia.pa',
            ],
            [
                'id' => 5,
                'nombrePrimario' => 'Carlos',
                'nombreSecundario' => 'Alberto',
                'apellidoPrimario' => 'Fernandez',
                'apellidoSecundario' => 'Diaz',
                'cedula' => '10-321-987',
                'correo' => 'carlos.fernandez@academia.pa',
            ],
            [
                'id' => 6,
                'nombrePrimario' => 'Laura',
                'nombreSecundario' => 'Beatriz',
                'apellidoPrimario' => 'Ruiz',
                'apellidoSecundario' => 'Alvarez',
                'cedula' => '2-654-321',
                'correo' => 'laura.ruiz@academia.pa',
            ],
            [
                'id' => 7,
                'nombrePrimario' => 'Jorge',
                'nombreSecundario' => 'Luis',
                'apellidoPrimario' => 'Moreno',
                'apellidoSecundario' => 'Jimenez',
                'cedula' => '6-111-222',
                'correo' => 'jorge.moreno@academia.pa',
            ],
            [
                'id' => 8,
                'nombrePrimario' => 'Sofia',
                'nombreSecundario' => 'Catalina',
                'apellidoPrimario' => 'Hernandez',
                'apellidoSecundario' => 'Vazquez',
                'cedula' => '12-333-444',
                'correo' => 'sofia.hernandez@academia.pa',
            ],
            [
                'id' => 9,
                'nombrePrimario' => 'Miguel',
                'nombreSecundario' => 'Angel',
                'apellidoPrimario' => 'Gomez',
                'apellidoSecundario' => 'Navarro',
                'cedula' => '1-555-666',
                'correo' => 'miguel.gomez@academia.pa',
            ],
            [
                'id' => 10,
                'nombrePrimario' => 'Elena',
                'nombreSecundario' => 'Patricia',
                'apellidoPrimario' => 'Torres',
                'apellidoSecundario' => 'Ramos',
                'cedula' => '13-777-888',
                'correo' => 'elena.torres@academia.pa',
            ],
            [
                'id' => 11,
                'nombrePrimario' => 'David',
                'nombreSecundario' => 'Alejandro',
                'apellidoPrimario' => 'Castro',
                'apellidoSecundario' => 'Iglesias',
                'cedula' => '5-999-000',
                'correo' => 'david.castro@academia.pa',
            ],
            [
                'id' => 12,
                'nombrePrimario' => 'Isabel',
                'nombreSecundario' => 'Cristina',
                'apellidoPrimario' => 'Ortega',
                'apellidoSecundario' => 'Serrano',
                'cedula' => '7-246-810',
                'correo' => 'isabel.ortega@academia.pa',
            ],
            [
                'id' => 13,
                'nombrePrimario' => 'Javier',
                'nombreSecundario' => 'Francisco',
                'apellidoPrimario' => 'Guerrero',
                'apellidoSecundario' => 'Molina',
                'cedula' => '11-135-791',
                'correo' => 'javier.guerrero@academia.pa',
            ],
            [
                'id' => 14,
                'nombrePrimario' => 'Carmen',
                'nombreSecundario' => 'Dolores',
                'apellidoPrimario' => 'Cano',
                'apellidoSecundario' => 'Garrido',
                'cedula' => '8-369-258',
                'correo' => 'carmen.cano@academia.pa',
            ],
            [
                'id' => 15,
                'nombrePrimario' => 'Adrian',
                'nombreSecundario' => 'Manuel',
                'apellidoPrimario' => 'Santos',
                'apellidoSecundario' => 'Castillo',
                'cedula' => '4-147-258',
                'correo' => 'adrian.santos@academia.pa',
            ],
            [
                'id' => 16,
                'nombrePrimario' => 'Raquel',
                'nombreSecundario' => 'Teresa',
                'apellidoPrimario' => 'Leon',
                'apellidoSecundario' => 'Cruz',
                'cedula' => '9-369-147',
                'correo' => 'raquel.leon@academia.pa',
            ],
            [
                'id' => 17,
                'nombrePrimario' => 'Fernando',
                'nombreSecundario' => 'Javier',
                'apellidoPrimario' => 'Prieto',
                'apellidoSecundario' => 'Pascual',
                'cedula' => '3-258-369',
                'correo' => 'fernando.prieto@academia.pa',
            ],
            [
                'id' => 18,
                'nombrePrimario' => 'Beatriz',
                'nombreSecundario' => 'Pilar',
                'apellidoPrimario' => 'Gallego',
                'apellidoSecundario' => 'Blanco',
                'cedula' => '10-147-369',
                'correo' => 'beatriz.gallego@academia.pa',
            ],
            [
                'id' => 19,
                'nombrePrimario' => 'Ricardo',
                'nombreSecundario' => 'Antonio',
                'apellidoPrimario' => 'Sanz',
                'apellidoSecundario' => 'Marin',
                'cedula' => '2-369-258',
                'correo' => 'ricardo.sanz@academia.pa',
            ],
            [
                'id' => 20,
                'nombrePrimario' => 'Veronica',
                'nombreSecundario' => 'Isabel',
                'apellidoPrimario' => 'Calvo',
                'apellidoSecundario' => 'Vega',
                'cedula' => '6-258-147',
                'correo' => 'veronica.calvo@academia.pa',
            ],
        ];

        foreach ($profesores as $profesor) {
            Profesor::create([
                'id' => $profesor['id'],
                'nombrePrimario' => $profesor['nombrePrimario'],
                'nombreSecundario' => $profesor['nombreSecundario'],
                'apellidoPrimario' => $profesor['apellidoPrimario'],
                'apellidoSecundario' => $profesor['apellidoSecundario'],
                'cedula' => $profesor['cedula'],
                'correo' => $profesor['correo'],
                'contrasena' => Hash::make('password123'),
            ]);
        }
    }
}

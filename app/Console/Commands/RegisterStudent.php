<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterStudent extends Command
{
    /**
     * El nombre y la firma del comando.
     *
     * @var string
     */
    protected $signature = 'academy:register {name} {email} {password?}';

    /**
     * La descripción del comando.
     *
     * @var string
     */
    protected $description = 'Registra un nuevo alumno en la Keiyi Academy';

    /**
     * Ejecuta el comando.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $password = $this->argument('password') ?? Str::random(12);

        if (User::where('email', $email)->exists()) {
            $this->error("El correo {$email} ya está registrado.");
            return;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'email_verified_at' => now(), // Verificación automática para alumnos
        ]);

        $this->info("¡Alumno registrado con éxito!");
        $this->line("Nombre: {$name}");
        $this->line("Email: {$email}");
        $this->line("Password: {$password}");
        $this->info("Ahora puede acceder a keiyi.digital/academy");
    }
}

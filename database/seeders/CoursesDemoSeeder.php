<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CoursesDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Categories
        $cat1 = \App\Modules\Courses\Models\CourseCategory::firstOrCreate(['name' => 'Ventas y Atención'], ['description' => 'Técnicas de ventas', 'active' => true]);
        $cat2 = \App\Modules\Courses\Models\CourseCategory::firstOrCreate(['name' => 'Operaciones'], ['description' => 'Manuales de piso', 'active' => true]);

        $admin = \App\Modules\Users\Models\User::first();

        // 2. Course
        $course = \App\Modules\Courses\Models\Course::firstOrCreate(
            ['title' => 'Técnicas de Venta en Piso'],
            [
                'slug' => 'tecnicas-de-venta-en-piso-demo',
                'description' => 'Aprende las mejores técnicas para cerrar ventas cuando el cliente visita nuestra sucursal. Ideal para asesores de mostrador.',
                'course_category_id' => $cat1->id,
                'status' => 'published',
                'created_by' => $admin->id ?? 1,
            ]
        );

        // 3. Lessons
        if ($course->lessons()->count() === 0) {
            $lesson1 = $course->lessons()->create([
                'title' => 'Introducción al abordaje',
                'description' => 'Cómo saludar al cliente en sus primeros 10 segundos.',
                'order' => 1,
                'is_required' => true,
                'created_by' => $admin->id ?? 1,
            ]);

            $lesson1->contents()->create([
                'title' => 'Video Explicativo',
                'content_type' => 'youtube',
                'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'order' => 1,
            ]);

            $lesson1->contents()->create([
                'title' => 'Manual de Saludo (PDF)',
                'content_type' => 'link',
                'url' => 'https://example.com/manual.pdf',
                'order' => 2,
            ]);

            $lesson2 = $course->lessons()->create([
                'title' => 'Manejo de Objeciones',
                'description' => 'Qué decir cuando el cliente dice "está muy caro".',
                'order' => 2,
                'is_required' => true,
                'created_by' => $admin->id ?? 1,
            ]);
        }

        // 4. Assessment
        if ($course->assessments()->count() === 0) {
            $exam = $course->assessments()->create([
                'title' => 'Examen Final de Ventas',
                'type' => 'exam',
                'min_score' => 80,
                'attempts_allowed' => 3,
            ]);

            $q1 = $exam->questions()->create([
                'question_text' => '¿Cuál es el tiempo ideal para abordar a un cliente que acaba de entrar?',
                'points' => 10,
                'order' => 1,
            ]);

            $q1->options()->create(['option_text' => 'Inmediatamente', 'is_correct' => false]);
            $q1->options()->create(['option_text' => 'Entre 10 y 15 segundos', 'is_correct' => true]);
            $q1->options()->create(['option_text' => 'Esperar a que él pregunte', 'is_correct' => false]);
        }
    }
}

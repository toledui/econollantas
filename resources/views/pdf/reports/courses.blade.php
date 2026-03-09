<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Reporte de Cursos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            color: #363d82;
            margin: 0 0 5px 0;
            font-size: 18px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f3f4f6;
            color: #363d82;
            font-weight: bold;
        }

        .text-left {
            text-align: left;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Reporte General de Cursos</h1>
        <p>Generado el: {{ date('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-left">Curso</th>
                <th>Asignados</th>
                <th>En Progreso</th>
                <th>Completados</th>
                <th>No Iniciados</th>
                <th>Revocados</th>
                <th>Eficiencia (%)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($courses as $course)
                @php
                    $notStarted = $course->total_assigned - ($course->completed_count + $course->in_progress_count);
                    $efficiency = $course->total_assigned > 0 ? floor(($course->completed_count / $course->total_assigned) * 100) : 0;
                @endphp
                <tr>
                    <td class="text-left">
                        {{ $course->title }}
                        @if($course->status !== 'published')
                            <span
                                style="color: #6b7280; font-size: 9px; text-transform: uppercase; margin-left: 4px;">(Borrador)</span>
                        @endif
                    </td>
                    <td>{{ $course->total_assigned }}</td>
                    <td>{{ $course->in_progress_count }}</td>
                    <td>{{ $course->completed_count }}</td>
                    <td>{{ $notStarted }}</td>
                    <td>{{ $course->revoked_count }}</td>
                    <td>{{ $efficiency }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Reporte de Alumnos</title>
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
            color: #0ea5e9;
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
            color: #0ea5e9;
            font-weight: bold;
        }

        .text-left {
            text-align: left;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Reporte de Avance Individual (Alumnos)</h1>
        <p>Generado el: {{ date('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-left">Empleado</th>
                <th class="text-left">Rol / Correo</th>
                <th>Sucursal</th>
                <th>Departamento</th>
                <th>Cursos Asignados</th>
                <th>Cursos Completados</th>
                <th>Progreso Promedio</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                @php
                    $assigned = $user->courseEnrollments->count();
                    $completed = $user->courseEnrollments->where('status', 'completed')->count();
                    $totalProgress = $user->courseEnrollments->sum('progress_percent');
                    $averageProgress = $assigned > 0 ? floor($totalProgress / $assigned) : 0;
                @endphp
                <tr>
                    <td class="text-left" style="font-weight: bold;">
                        {{ $user->name }}
                        @if($user->status === 'inactive')
                            <span style="color: #dc2626; font-size: 9px; text-transform: uppercase;">(Baja)</span>
                        @endif
                    </td>
                    <td class="text-left" style="font-size: 10px;">{{ $user->email }}</td>
                    <td>{{ $user->primaryBranch->name ?? 'Sin Sucursal' }}</td>
                    <td>{{ $user->department->name ?? 'Sin Departamento' }}</td>
                    <td>{{ $assigned }}</td>
                    <td>{{ $completed }}</td>
                    <td>{{ $averageProgress }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
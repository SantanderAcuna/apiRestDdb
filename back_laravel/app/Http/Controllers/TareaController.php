<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tarea;

class TareaController extends Controller
{
    /**
     * Muestra una lista de tareas.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $tareas = Tarea::all();

        return response()->json($tareas, 200);
    }

    /**
     * Retorna la información básica de una tarea en formato JSON.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $tarea = Tarea::findOrFail($id);

        return response()->json([
            'titulo' => $tarea->titulo,
            'descripcion' => $tarea->descripcion,
            'fecha_inicio' => $tarea->fecha_inicio,
            'fecha_finalizacion' => $tarea->fecha_finalizacion,
            'archivo_adjunto' => $tarea->archivo_adjunto,
            'estado' => $tarea->estado,
        ], 200);
    }

    /**
     * Almacena una nueva tarea.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Valida los datos del formulario
        $request->validate([
            'titulo' => 'required|max:255',
            'descripcion' => 'required',
            'estado' => 'required|in:pendiente,en progreso,completada',
            'fecha_inicio' => 'nullable|date',
            'fecha_finalizacion' => 'nullable|date|after_or_equal:fecha_inicio',
            'archivo_adjunto' => 'nullable|file|mimes:pdf,doc,docx',

            'estado.in' => 'El estado debe ser uno de los valores: pendiente, en progreso, completada.',
            'fecha_finalizacion.after_or_equal' => 'La fecha de finalización debe ser posterior o igual a la fecha de inicio.',
            'archivo_adjunto.mimes' => 'El archivo adjunto debe ser un PDF o un documento de Word.',
        ]);

        // Crea una nueva tarea
        $tarea = new Tarea;
        $tarea->fill($request->only(['titulo', 'descripcion', 'estado', 'fecha_inicio', 'fecha_finalizacion']));

        // Subir y guardar el archivo adjunto si está presente
        if ($request->hasFile('archivo_adjunto')) {
            $archivo = $request->file('archivo_adjunto');
            $archivo->storeAs('archivos_adjuntos', $archivo->getClientOriginalName());
            $tarea->archivo_adjunto = $archivo->getClientOriginalName();
        }

        $tarea->save();

        return response()->json(['message' => 'Tarea creada exitosamente'], 201);
    }

    /**
     * Actualiza una tarea existente.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Valida los datos del formulario
        $request->validate([
            'titulo' => 'required|max:255',
            'descripcion' => 'required',
            'estado' => 'required|in:pendiente,en progreso,completada',
            'fecha_inicio' => 'nullable|date',
            'fecha_finalizacion' => 'nullable|date|after_or_equal:fecha_inicio',
            'archivo_adjunto' => 'nullable|file|mimes:pdf,doc,docx', // Cambiar los tipos de archivo permitidos si es necesario
        ], [
            'estado.in' => 'El estado debe ser uno de los valores: pendiente, en progreso, completada.',
            'fecha_finalizacion.after_or_equal' => 'La fecha de finalización debe ser posterior o igual a la fecha de inicio.',
            'archivo_adjunto.mimes' => 'El archivo adjunto debe ser un PDF o un documento de Word.',
        ]);

        $tarea = Tarea::findOrFail($id);
        $tarea->fill($request->only(['titulo', 'descripcion', 'estado', 'fecha_inicio', 'fecha_finalizacion']));

        // Subir y guardar el archivo adjunto si está presente
        if ($request->hasFile('archivo_adjunto')) {
            $archivo = $request->file('archivo_adjunto');
            $archivo->storeAs('archivos_adjuntos', $archivo->getClientOriginalName());
            $tarea->archivo_adjunto = $archivo->getClientOriginalName();
        }

        $tarea->save();

        return response()->json(['message' => 'Tarea actualizada exitosamente'], 200);
    }


    public function destroy($id)
    {
        $tarea = Tarea::findOrFail($id);

        // Eliminar archivo adjunto si existe
        if (!empty($tarea->archivo_adjunto)) {
            // Eliminar el archivo físico almacenado
            $rutaArchivo = storage_path('app/archivos_adjuntos/' . $tarea->archivo_adjunto);
            if (file_exists($rutaArchivo)) {
                unlink($rutaArchivo);
            }
        }

        $tarea->delete();

        return response()->json(['message' => 'Tarea eliminada exitosamente'], 200);
    }
}

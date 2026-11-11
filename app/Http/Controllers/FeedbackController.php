<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $feedbacks = Feedback::all();
        return response()->json($feedbacks);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'id_usuario' => 'required|exists:usuarios,id',
            'id_servico' => 'required|exists:servicos,id',
            'assunto' => 'required|string|max:255',
            'comentario' => 'required|string',
            'categoria' => 'required|in:elogio,reclamacao',
            'prioridade' => 'required|in:baixa,media,alta',
            'status' => 'required|in:pendente,em analise,resolvido',
        ]);

        try {
            $feedback = Feedback::create($validatedData);

            return response()->json([
                'message' => 'Feedback criado com sucesso!',
                'feedback' => $feedback
            ], 201);

        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Falha ao criar feedback',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Feedback $feedback)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Feedback $feedback)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Feedback $feedback)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Feedback $feedback)
    {
        //
    }
}

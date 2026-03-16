<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        return response()->json($request->user()->tickets, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $ticket = $request->user()->tickets()->create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'open',
        ]);

        return response()->json($ticket, Response::HTTP_CREATED);
    }

    public function show(Request $request, Ticket $ticket)
    {
        if ($ticket->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        return response()->json($ticket, Response::HTTP_OK);
    }

    public function suggestResponse(Request $request, Ticket $ticket)
    {
        if ($ticket->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        $apiKey = env('OPENAI_API_KEY', 'dummy_key');

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful customer support agent. Provide a short, direct suggested solution for the following customer ticket description.'],
                    ['role' => 'user', 'content' => $ticket->description],
                ],
            ]);

            if ($response->successful()) {
                $suggestion = $response->json()['choices'][0]['message']['content'];
                return response()->json(['suggestion' => $suggestion, 'is_mock' => false], Response::HTTP_OK);
            }

            $errorDetail = $response->json();
            $errorMessage = $errorDetail['error']['message'] ?? 'Unknown OpenAI error';

            return response()->json([
                'suggestion' => 'This is a mock AI response since the API call failed. The suggested solution is to restart your system and check the logs.',
                'error_message' => "OpenAI Error: " . $errorMessage,
                'is_mock' => true
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'suggestion' => 'This is a mock AI response due to an exception while calling the AI API. Try checking your network connection.',
                'error_message' => "Exception: " . $e->getMessage(),
                'is_mock' => true
            ], Response::HTTP_OK);
        }
    }

    public function close(Request $request, Ticket $ticket)
    {
        if ($ticket->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        $ticket->update(['status' => 'closed']);

        return response()->json($ticket, Response::HTTP_OK);
    }
}

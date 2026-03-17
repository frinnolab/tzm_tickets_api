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

        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) {
            return response()->json([
                'suggestion' => 'Gemini API key is not configured.',
                'is_mock' => true
            ], Response::HTTP_OK);
        }

        try {
            $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-lite-latest:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => "You are a helpful customer support agent. Provide a short, direct suggested solution for the following customer ticket description:\n\n" . $ticket->description]
                        ]
                    ]
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $suggestion = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No suggestion generated.';
                return response()->json(['suggestion' => trim($suggestion), 'is_mock' => false], Response::HTTP_OK);
            }

            $errorDetail = $response->json();
            $errorMessage = $errorDetail['error']['message'] ?? 'Unknown Gemini error';

            return response()->json([
                'suggestion' => 'This is a mock AI response since the API call failed. Direct solution: Try clearing cache and cookies.',
                'error_message' => "Gemini Error: " . $errorMessage,
                'is_mock' => true
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'suggestion' => 'This is a mock AI response due to an exception while calling the AI API.',
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

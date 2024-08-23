<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VoiceController extends Controller
{
    public function synthesize(Request $request)
    {
        try {
            $text = $request->input('text');

            // Log the text input to ensure it's received correctly
            Log::info('Text input for synthesis: ' . $text);

            // Use eSpeak command to synthesize speech
            $outputFile = storage_path('app/public/georgian_speech.wav');
            $command = "espeak -v ka -w {$outputFile} '{$text}'";

            // Execute the command
            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                throw new \Exception('Failed to synthesize speech.');
            }

            // Log success message
            Log::info('Text-to-Speech synthesis successful.');

            // Return the synthesized audio file path
            return response()->download($outputFile, 'georgian_speech.wav');
        } catch (\Exception $e) {
            // Log the error message
            Log::error('Error during synthesis: ' . $e->getMessage());
            return response()->json(['error' => 'Error during synthesis: ' . $e->getMessage()], 500);
        }
    }
}

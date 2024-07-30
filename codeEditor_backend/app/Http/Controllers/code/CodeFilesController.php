<?php

namespace App\Http\Controllers\code;

use App\Http\Controllers\Controller;
use App\Models\code_file;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class CodeFilesController extends Controller
{
    public function getUserCodes(Request $request)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Token not provided'], 401);
        }

        try {
            // Decode the token to get the user ID
            $payload = JWTAuth::setToken($token)->getPayload();
            $userId = $payload['sub']; // Assuming 'sub' is the claim that holds the user ID

            // Retrieve the codes associated with the user ID
            $codes = code_file::where('user_id', $userId)->get();

            return response()->json([
                'status' => 'success',
                'data' => $codes
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Could not fetch user codes'], 500);
        }
    }
    public function updateCode(Request $request)
    {
        // Validate the request data
        $request->validate([
            'id' => 'required|integer',
            'language' => 'sometimes|required|string',
            'source_code' => 'sometimes|required|string',
        ]);

        // Get the token from the request headers
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Token not provided'], 401);
        }

        try {
            // Decode the token to get the user ID
            $payload = JWTAuth::setToken($token)->getPayload();
            $userId = $payload['sub']; // Assuming 'sub' is the claim that holds the user ID

            // Retrieve the code file by ID and user ID
            $codeFile = code_file::where('id', $request->input('id'))->where('user_id', $userId)->first();

            if (!$codeFile) {
                return response()->json(['error' => 'Code file not found or unauthorized'], 404);
            }

            // Update the code file with provided data
            if ($request->has('language')) {
                $codeFile->language = $request->input('language');
            }
            if ($request->has('source_code')) {
                $codeFile->source_code = $request->input('source_code');
            }
            $codeFile->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Code file updated successfully',
                'data' => $codeFile
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Could not update code file'], 500);
        }
    }
    public function getCodeFileById(Request $request)
    {
        // Validate the request data
        $request->validate([
            'id' => 'required|integer',
        ]);

        // Get the token from the request headers
        $token = $request->bearerToken();
        $id = $request->input('id');

        if (!$token) {
            return response()->json(['error' => 'Token not provided'], 401);
        }

        try {
            // Decode the token to get the user ID
            $payload = JWTAuth::setToken($token)->getPayload();
            $userId = $payload['sub']; // Assuming 'sub' is the claim that holds the user ID

            // Retrieve the code file by ID and check if it belongs to the user
            $codeFile = code_file::where('id', $id)->where('user_id', $userId)->first();

            if (!$codeFile) {
                return response()->json(['error' => 'Code file not found or unauthorized'], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $codeFile
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Could not fetch code file'], 500);
        }
    }
}

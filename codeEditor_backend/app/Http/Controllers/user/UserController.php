<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use League\Csv\Reader;

class UserController extends Controller
{
    public function getAllUsers()
    {
        $users = User::select('id', 'username', 'email', 'role')->get();

        return response()->json($users);
    }
    public function deleteUser(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);

        $id = $request->input('id');
        $user = User::find($id);

        if ($user) {
            $user->delete();
            return response()->json(['message' => 'User deleted successfully']);
        }

        return response()->json(['message' => 'User not found'], 404);
    }
    public function uploadUsersCsv(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid file upload'], 400);
        }

        try {
            $file = $request->file('file')->getRealPath();
            $csv = Reader::createFromPath($file, 'r');
            $csv->setHeaderOffset(0);
            $records = $csv->getRecords();

            $errors = [];
            foreach ($records as $index => $record) {
                $validation = Validator::make($record, [
                    'username' => 'required|string|unique:users,username',
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required|string|min:6',
                ]);

                if ($validation->fails()) {
                    $errors[$index] = $validation->errors()->all();
                    continue;
                }

                User::create([
                    'username' => $record['username'],
                    'email' => $record['email'],
                    'password' => bcrypt($record['password']),
                    'role' => $record['role'] ?? 'user', // Default role if not provided
                ]);
            }

            if (!empty($errors)) {
                return response()->json(['message' => 'Some records failed', 'errors' => $errors], 422);
            }

            return response()->json(['message' => 'File processed successfully']);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error processing file', 'error' => $e->getMessage()], 500);
        }
    }
}

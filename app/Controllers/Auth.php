<?php

namespace App\Controllers;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use CodeIgniter\API\ResponseTrait;
use App\Models\UserModel;

class Auth extends BaseController
{
    use ResponseTrait;

    private function generateJWT($data)
    {
        $key = getenv('TOKEN_SECRET');
        $payload = array(
            "iss" => "localhost",
            "iat" => time(),
            "exp" => time() + 3600,  // Token berlaku selama 1 jam
            "data" => $data
        );

        return JWT::encode($payload, $key, 'HS256');
    }

    private function validateJWT()
    {
        $key = getenv('TOKEN_SECRET');
    
        // Try to get the token from the Authorization header
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        $matches = [];
        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];
        } else {
            // If not in the header, try to get it from the request body
            $inputJSON = file_get_contents('php://input');
            $input = json_decode($inputJSON, TRUE);
            $token = $input['token'] ?? null;
        }
    
        try {
            if (!$token) {
                throw new \Exception('Token not provided');
            }
    
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            return $decoded;
        } catch (\Exception $e) {
            error_log("JWT Error: " . $e->getMessage());
            return false;
        }
    }
    
    

    public function verifyToken()
    {
        $isValid = $this->validateJWT();
    
        if ($isValid) {
            return $this->respond(['isValid' => true]);
        } else {
            error_log("Token validation failed.");
            return $this->respond(['isValid' => false], 401);
        }
    }
    

    public function register()
    {
        $model = new UserModel();
    
        // Get the raw JSON input
        $jsonInput = $this->request->getBody();
    
        // Decode the JSON into an associative array
        $inputData = json_decode($jsonInput, true);
    
        if (!isset($inputData['username']) || !isset($inputData['password'])) {
            return $this->failValidationError('Username and password are required.');
        }
    
        $data = [
            'username' => $inputData['username'],
            'password' => password_hash($inputData['password'], PASSWORD_BCRYPT),
        ];
    
        if ($model->insert($data)) {
            return $this->respondCreated(['message' => 'Registration successful']);
        } else {
            return $this->failServerError('Registration failed');
        }
    }
    

    public function login()
    {
        $model = new UserModel();
    
        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON, TRUE); // convert JSON into array
    
        $username = isset($input['username']) ? $input['username'] : null;
        $password = isset($input['password']) ? $input['password'] : null;
    
        $user = $model->where('username', $username)->first();
    
        if ($user && password_verify($password, $user['password'])) {
            return $this->respond(['token' => $this->generateJWT($user)]);
        } else {
            return $this->failUnauthorized('Invalid credentials');
        }
    }
    
    public function getUserDetails()
    {
        $decoded = $this->validateJWT();
        if (!$decoded) {
            return $this->failUnauthorized('Token is invalid or expired');
        }

        // Extract user details from the decoded token
        $userDetails = (array) $decoded->data;

        // Optionally, if you wanted to fetch more details from the database using some ID or key in the token:
        // $user = $model->find($userDetails['id']);
        // And then return those details.

        return $this->respond(['user' => $userDetails]);
    }

}

<?php

namespace App\Controllers;

use App\Models\User;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Tuupola\Middleware\JwtAuthentication;
use Firebase\JWT\JWT;
use \Datetime;
use Valitron\Validator;
use Slim\Exception\HttpUnauthorizedException;

class AuthController
{
    private $container;

    /**
     * AuthController constructor.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     *
     * Save a user in database
     *
     * @OA\Post(
     *     tags={"User"},
     *     path="/api/v1/user/create",
     *     operationId="storeUser",
     *     @OA\Parameter(
     *          name="name",
     *          in="query",
     *          required=true,
     *          description="The user name",
     *          @OA\Schema(
     *              type="string"
     *          ),
     *     ),
     *     @OA\Parameter(
     *          name="email",
     *          in="query",
     *          required=true,
     *          description="The user email",
     *          @OA\Schema(
     *              type="string"
     *          ),
     *     ),
     *      @OA\Parameter(
     *          name="password",
     *          in="query",
     *          required=true,
     *          description="The user password",
     *          @OA\Schema(
     *              type="string"
     *          ),
     *     ),
     *      @OA\Response(
     *          response="201",
     *          description="Created",
     *          @OA\JsonContent(
     *              type="object",
     *              ref="#/components/schemas/User",
     *          )
     *     ),
     *      @OA\Response(
     *      response="409",
     *      description="Conflict",
     *      @OA\JsonContent(
     *           type="object",
     *           example="{ 'validation': { 'name': [ 'Name is required' ], 'email': [ 'Email is not a valid email address']} }"))
     *     )
     * )
     */
    public function store(Request $request, Response $response, array $args): Response
    {
        $params = json_decode($request->getBody());

        $user_data = [];
        $user_data['name'] = filter_var($params->name, FILTER_SANITIZE_STRING);
        $user_data['email'] = filter_var($params->email, FILTER_SANITIZE_STRING);
        $user_data['password'] = filter_var($params->password, FILTER_SANITIZE_STRING);

        $validator = new Validator($user_data);
        $validator->rule('required', ['name', 'email', 'password']);
        $validator->rule('email', 'email');

        // If input is correct save user otherwise send validation errors
        if($validator->validate()) {
            // Hash the password
            $user_data['password'] =  password_hash($user_data['password'], PASSWORD_BCRYPT);
            $user = new User($user_data);
            // Save user
            if ($user->save()) {
                $result['id'] = $user->id;
                $result['name'] = $user->name;
                $result['email'] = $user->email;
                $response->getBody()->write(json_encode($result));
                return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(201);
            }
        }
        // If there are validation errors
        $errors['validation'] = $validator->errors();
        $response->getBody()->write(json_encode($errors));
        return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(409);
    }

    /**
     *
     * Sign in an user and return token
     *
     * @OA\Post(
     *     tags={"User"},
     *     path="/api/v1/user/signin",
     *     operationId="signinUser",
     *     @OA\Parameter(
     *          name="email",
     *          in="query",
     *          required=true,
     *          description="The user email",
     *          @OA\Schema(
     *              type="string"
     *          ),
     *     ),
     *      @OA\Parameter(
     *          name="password",
     *          in="query",
     *          required=true,
     *          description="The user password",
     *          @OA\Schema(
     *              type="string"
     *          ),
     *     ),
     *     @OA\Response(
     *      response="200",
     *      description="Ok",
     *      @OA\JsonContent(
     *           type="object",
     *           example="{ 'token': 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MiwiZW1haWwiOiJjaGVvQGFiczEyMy5jb20iLCJpYXQiOjE2NTA3MTE3NTIsIm5iZiI6MTY1MDcxMTc1Mn0.irJpufliLpUKZ5LE8aUaDci5mY5vo0BfEFWbTKj9n4Q' }"
     *          ),
     *     ),
     *      @OA\Response(
     *      response="401",
     *      description="Unauthorized"
     *     )
     * )
     *
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function signin(Request $request, Response $response, array $args): Response
    {

        $params = json_decode($request->getBody());

        // Validate user and password
        $user = User::where('email', $params->email);

        if ($user->count() == 1) {
            $auth = password_verify($params->password, $user->first()->password);
        } else {
             // If not exist credentials send 401
            throw new HttpUnauthorizedException($request);
        }

        if ($auth) {
            $issued_at = new DateTime();
            $token = JWT::encode(
                [
                    'id'    => $user->first()->id,
                    'email' => $user->first()->email,
                    'iat'   => $issued_at->getTimestamp(),
                    'nbf'   => $issued_at->getTimestamp(),
                ],
                $_ENV['SECRET_TOKEN'],
                'HS256'
            );
            $response->getBody()->write(json_encode(['token' => $token]));
            return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(200);
        }
        // If not valid credentials send 401
        throw new HttpUnauthorizedException($request);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Validation error response
     *
     * @param array $errors
     * @return \Illuminate\Http\Response
     */
    public function responseValidationError(array $errors = ['message' => 'Validation error.'])
    {
        return response()->json($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Unauthenticated response with message
     *
     * @param array $errors = []
     * @param string $msg = 'Error'
     * @param integer $code = 500
     * @return \Illuminate\Http\Response
     */
    public function responseUnauthenticated($msg = "Unauthenticated.")
    {
        $data = [
            "message" => $msg
        ];

        return response()->json($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Resource not found respons with message
     *
     * @param string $msg 
     * @return \Illuminate\Http\Response
     */
    public function responseNotFound(string $msg = "Resource not found")
    {
        $data = [
            "message" => $msg
        ];

        return response()->json($data, Response::HTTP_NOT_FOUND);
    }

    /**
     * Success response message
     *
     * @param array $data = []
     * @param integer $code = 200
     * @return \Illuminate\Http\JsonResponse
     */
    public function responseSuccess($data = [], $code = Response::HTTP_OK): JsonResponse
    {
        return response()->json($data, $code);
    }

    /**
     * Success response message
     *
     * @param array $data = []
     * @return \Illuminate\Http\Response
     */
    public function responseCreated($data = []): Response
    {
        return response()->json($data, Response::HTTP_CREATED);
    }

    /**
     * Success response message
     *
     * @param array $data = []
     * @return \Illuminate\Http\Response
     */
    public function responseNoContent($data = ['message' => 'Success'])
    {
        return response()->json($data, Response::HTTP_NO_CONTENT);
    }

    /**
     * Unauthorized access
     *
     * @param array $data = []
     * @return \Illuminate\Http\Response
     */
    public function responseUnauthorized($data = [])
    {
        return response()->json($data, Response::HTTP_FORBIDDEN);
    }
    
    /**
     * Forbidden access
     *
     * @param array $data = []
     * @return \Illuminate\Http\Response
     */
    public function responseForbidden($data = ['message' => 'Access Denied'])
    {
        return response()->json($data, Response::HTTP_FORBIDDEN);
    }
    
    /**
	 * Returns real client ip
	 *
	 * @return string
	 */
	public function getIp(){
		return self::getClientIp();
	}
	
	/**
	 * Returns real client ip
	 *
	 * @return string
	 */
	public static function getClientIp()
	{
		$httpHeaders = [
			'HTTP_X_FORWARDED_FOR', 
			'HTTP_CLIENT_IP', 
			'HTTP_X_FORWARDED', 
			'HTTP_X_CLUSTER_CLIENT_IP', 
			'HTTP_FORWARDED_FOR', 
			'HTTP_FORWARDED', 
			'REMOTE_ADDR'
		];
		foreach ($httpHeaders as $key){
			if (array_key_exists($key, $_SERVER) === true){
				foreach (explode(',', $_SERVER[$key]) as $ip){
					$ip = trim($ip); // just to be safe
					if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
						return $ip;
					}
				}
			}
		}
		return request()->ip(); // it will return server ip when no client ip found
	}
}

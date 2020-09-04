<?php

namespace App\Components\Traits;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

trait ApiController {

	/**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result = [], $message)
    {
        $response = [
            'status' => true,
            'type'    => 'success',
            'msg' => $message,
        ];

        $arrResult = !is_array($result) ? $result->toArray() : $result;

        if(array_key_exists('current_page', $arrResult)){
            $response = array_merge($response,$arrResult);
        }else{
            $response['data'] = $arrResult;
        }


        return response()->json($response, 200);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'status' => false,
            'type'    => 'failed',
            'msg' => $error,
        ];


        if(!empty($errorMessages)){
            if($errorMessages instanceof ValidationException){
                $response['msg'] = collect($errorMessages->errors())->first()[0];
            }else{
                if ( method_exists($errorMessages, 'getMessage') && is_callable([$errorMessages, 'getMessage'])) {
                    $response['msg'] = $errorMessages->getMessage();
                } else {
                    $response['msg'] = $errorMessages;
                }
                // if(is_array($errorMessages)){
                //     $response['data'] = $errorMessages;
                // }else{
                //     $response['data'][] = $errorMessages;
                // }
            }

        }


        return response()->json($response, $code);
    }

}
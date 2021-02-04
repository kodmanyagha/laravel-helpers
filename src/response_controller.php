<?php

namespace Kodmanyagha\Helpers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class ResponseController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    const SUCCESS = 'success';
    const PARTIALLY_SUCCESS = 'partially_success';
    const WARNING = 'warning';
    const ERROR = 'error';
    const INPUT_NOT_VALID = 'input_not_valid';
    /***
     *
     * @var mixed
     */
    protected $data;

    protected function set($key, $data)
    {
        $this->data->{$key} = $data;
    }

    protected function setData($data)
    {
        $this->data = $data;
    }

    protected function sendResponse($status, $response = null, $code = 200)
    {
        if (\is_null($response))
            $response = &$this->data;

        return response()->json([
            'status' => $status,
            'data'   => $response
        ], $code);
    }

    protected function success($response = null)
    {
        return $this->sendResponse(ResponseController::SUCCESS, $response);
    }

    protected function error($errorMessage = null, $code = 400)
    {
        return response()->json([
            'status'       => "error",
            'errorMessage' => $errorMessage
        ], $code);
    }

    protected function printDT($data, $total, $filtered, $draw = 0)
    {
        $responseData                    = array();
        $responseData["recordsTotal"]    = (int)$total;
        $responseData["recordsFiltered"] = (int)$filtered;
        $responseData["data"]            = $data;
        $responseData["draw"]            = $draw;

        return response()->json($responseData, 200);
    }
}

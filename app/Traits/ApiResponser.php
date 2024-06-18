<?php

namespace App\Traits;

trait ApiResponser
{
    protected function success($data, $message = null, $code = 200)
    {
        return response()->json(
            [
                'success' => true,
                'message' => $message,
                'data' => $data,
            ],
            $code,
        );
    }

    protected function error($message = null, $code = null, $data = null)
    {
        return response()->json(
            [
                'success' => false,
                'message' => $message,
                'data' => $data,
            ],
            $code,
        );
    }
}

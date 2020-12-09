<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NoPrivilageException extends Exception
{
    public $request;
    public $message;

    public function __construct( array $message)
    {
        // 複数のバリデーションエラー時には , で区切る
        $this->message = implode(',', $message);
    }
    public function render()
    {
        return response()->json(['massage' =>
            $this->message],
            403
        );
    }
}

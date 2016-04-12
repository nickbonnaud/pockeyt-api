<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class Request extends FormRequest {

    public function forbiddenResponse() {
        flash()->error('Forbidden', 'You are not allowed to do that.');
        return redirect()->to(\URL::previous());
    }

}

<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidExeFile implements Rule
{
    public function passes($attribute, $value)
    {
        // Check if the file extension is .exe
        return strtolower($value->getClientOriginalExtension()) === 'exe';
    }

    public function message()
    {
        return 'The :attribute must be a file of type: exe.';
    }
}

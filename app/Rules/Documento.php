<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\User;

class Documento implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public $tipo;
    public $documento;
    public function __construct($tipo)
    {
        $this->tipo = $tipo;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $paciente = User::where('documento', $value)->where('id_doc', $this->tipo)->get();
        //dd(count($paciente));
        if(count($paciente) > 0){
            return false;
        }else{
            return true;
        }

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Este Documento ya se encuentra registrado';
    }
}

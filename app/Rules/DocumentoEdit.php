<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\User;
class DocumentoEdit implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public $data;
    public $id;
    public function __construct($data, $id)
    {
        $this->data = $data;
        $this->id = $id;
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
        //dd($this->data['id_doc']);
        $paciente = User::where('documento', $value)
            ->where('id_doc', $this->data['id_doc'])
            ->where('id', '<>', $this->id)
            ->get();
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

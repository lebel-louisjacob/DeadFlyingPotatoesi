<?php

namespace App\Rules;

use Bogardo\Mailgun\Facades\Mailgun;
use Illuminate\Contracts\Validation\Rule;

class IsNewEmail implements Rule
{
    private $listAdress;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->listAdress = 'testing_list@'.env('MAILGUN_DOMAIN');
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
        try {
            Mailgun::api()->get("lists/{$this->listAdress}/members/{$value}");
        }
        catch (\Exception $e) {
            return true;
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The email address already exist in the mailing list.';
    }
}

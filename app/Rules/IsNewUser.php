<?php

namespace App\Rules;

use App\Http\Repositories\UserRepository;
use Illuminate\Contracts\Validation\Rule;

class IsNewUser implements Rule
{
    private $userRepository;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->userRepository = app()->make(UserRepository::class);
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
        return !$this->userRepository->exist($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'An account with this email already exist.';
    }
}

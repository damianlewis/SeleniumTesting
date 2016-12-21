<?php

namespace SeleniumTesting\Concerns;

use App\User;

trait ImpersonatesUsers
{

    /**
     * Set the currently logged in user for the application.
     *
     * @param User   $user
     * @param string $password
     *
     * @return $this
     */
    protected function actingAs(User $user, string $password = 'password')
    {
        $this->visit('login')
            ->type($user->email, 'email')
            ->type($password, 'password')
            ->press('Login');

        return $this;
    }

}
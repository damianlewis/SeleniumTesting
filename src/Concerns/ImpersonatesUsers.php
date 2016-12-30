<?php

namespace SeleniumTesting\Concerns;

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
    public function actingAs($user, $password = 'password')
    {
        $this->visit('/login')
            ->type($user->email, 'email')
            ->type($password, 'password')
            ->press('Login');

        return $this;
    }

}
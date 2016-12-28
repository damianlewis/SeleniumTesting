<?php

abstract class SeleniumTestCase extends SeleniumTesting\SeleniumTestCase
{

    protected $browser = 'firefox';
    protected $baseUrl = 'http://seleniumtesting.dev';
    protected $host = 'seleniumtesting.dev';
    protected $capabilities = [
        'firefox_profile' => 'UEsDBAoAAAAAABlsiklH/38iJwAAACcAAAAIABwAcHJlZnMuanNVVAkAAwIETFiUBExYdXgLAAEE9QEAAAQUAAAAdXNlcl9wcmVmKCJhcHAudXBkYXRlLmVuYWJsZWQiLCBmYWxzZSk7UEsBAh4DCgAAAAAAGWyKSUf/fyInAAAAJwAAAAgAGAAAAAAAAQAAAKSBAAAAAHByZWZzLmpzVVQFAAMCBExYdXgLAAEE9QEAAAQUAAAAUEsFBgAAAAABAAEATgAAAGkAAAAAAA=='
    ];
    protected $screenShotPath = __DIR__.'/screenshots';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../tests/test-app/bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }
}
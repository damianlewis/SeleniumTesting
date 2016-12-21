<?php

abstract class SeleniumTestCase extends SeleniumTesting\SeleniumTestCase
{

    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://seleniumtesting.dev';

    /**
     * The browser to run the tests through.
     *
     * @var string
     */
    protected $browser = 'firefox';

    /**
     * A list of capabilities to set for the browser.
     *
     * @var array
     */
    protected $capabilities = [
        'firefox_profile' => 'UEsDBAoAAAAAABlsiklH/38iJwAAACcAAAAIABwAcHJlZnMuanNVVAkAAwIETFiUBExYdXgLAAEE9QEAAAQUAAAAdXNlcl9wcmVmKCJhcHAudXBkYXRlLmVuYWJsZWQiLCBmYWxzZSk7UEsBAh4DCgAAAAAAGWyKSUf/fyInAAAAJwAAAAgAGAAAAAAAAQAAAKSBAAAAAHByZWZzLmpzVVQFAAMCBExYdXgLAAEE9QEAAAQUAAAAUEsFBgAAAAABAAEATgAAAGkAAAAAAA=='
    ];

    /**
     * The path to use for saving screen shots.
     *
     * @var string
     */
    protected $screenShotPath = __DIR__.'/screenshots';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../tests/laravel/bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }
}
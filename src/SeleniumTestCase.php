<?php

namespace SeleniumTesting;

use PHPUnit_Extensions_Selenium2TestCase;
use PHPUnit_Framework_Error;
use PHPUnit_Framework_Exception;
use SeleniumTesting\Concerns\ImpersonatesUsers;
use SeleniumTesting\Concerns\InteractsWithPage;

abstract class SeleniumTestCase extends PHPUnit_Extensions_Selenium2TestCase
{

    use InteractsWithPage;
    use ImpersonatesUsers;

    /**
     * The Illuminate application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * Creates the application.
     *
     * Needs to be implemented by subclasses.
     *
     * @return \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    abstract public function createApplication();

    /**
     * Switches the application environment to testing.
     * Sets up the selenium test case and laravel application.
     */
    protected function setUp()
    {
        if (! $this->app) {
            $this->refreshApplication();
        }

        $this->setBrowser($this->browser);
        $this->setBrowserUrl($this->baseUrl);
        $this->setDesiredCapabilities($this->capabilities);

        if (! $this->app) {
            $this->app = $this->createApplication();
        }
    }

    /**
     * Refresh the application instance.
     *
     * @return void
     */
    protected function refreshApplication()
    {
        putenv('APP_ENV=testing');

        $this->app = $this->createApplication();
    }

    /**
     * Switches the application environment to local.
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * Throws an error when the test did not execute successfully.
     *
     * @param  Exception|Throwable $e
     *
     * @throws PHPUnit_Framework_Error
     */
    public function onNotSuccessfulTest($e)
    {
        if ($e instanceof PHPUnit_Framework_Exception) {
            $fileName = $this->screenShotPath.DIRECTORY_SEPARATOR.get_class($this).'_'.date('Y-m-d\TH-i-s').'.png';
            file_put_contents($fileName, $this->currentScreenshot());
            $error = new PHPUnit_Framework_Error("Screen shot for '{$this->url()}' saved to: {$fileName}",
                $e->getCode(), $e->getFile(), $e->getLine(), $e);
        } else {
            $error = $e;
        }

        parent::onNotSuccessfulTest($error);
    }
}
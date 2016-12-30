<?php

namespace SeleniumTesting;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Foundation\Testing\Concerns\InteractsWithConsole;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use InvalidArgumentException;
use PHPUnit_Extensions_Selenium2TestCase;
use PHPUnit_Framework_Error;
use PHPUnit_Framework_Exception;
use SeleniumTesting\Concerns\InteractsWithPage;

abstract class SeleniumTestCase extends PHPUnit_Extensions_Selenium2TestCase
{

    use InteractsWithPage;
    use InteractsWithConsole;

    /**
     * The Illuminate application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * The callbacks that should be run after the application is created.
     *
     * @var array
     */
    protected $afterApplicationCreatedCallbacks = [];

    /**
     * The callbacks that should be run before the application is destroyed.
     *
     * @var array
     */
    protected $beforeApplicationDestroyedCallbacks = [];

    /**
     * Indicates if we have made it through the base setUp function.
     *
     * @var bool
     */
    protected $setUpHasRun = false;

    /**
     * he browser to run the tests through.
     *
     * @var string
     */
    protected $browser;

    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl;

    /**
     * The host name for the connection.
     *
     * @var string
     */
    protected $host;

    /**
     * A list of capabilities to set for the browser.
     *
     * @var array
     */
    protected $capabilities;

    /**
     * The path to the folder where screen shots of the browser will be saved.
     *
     * @var string
     */
    protected $screenShotPath;

    /**
     * The path to the laravel .env environment file.
     *
     * @var string
     */
    protected $envFile;

    /**
     * The path to the testing environment file.
     * This file replaces the .env file whilst testing.
     *
     * @var string
     */
    protected $testingEnvFile;

    /**
     * The path to the local environment file.
     * This file replaces the .env file when testing has finished.
     *
     * @var string
     */
    protected $localEnvFile;

    /**
     * Indicates if the required environment files exist.
     *
     * @var string
     */
    protected $envFilesChecked = false;

    /**
     * Creates the application.
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
        $this->checkEnvFiles();
        $this->switchEnvironment($this->testingEnvFile);

        if (! $this->app) {
            $this->refreshApplication();
        }

        $this->setBrowser($this->browser);
        $this->setBrowserUrl($this->baseUrl);
        $this->setHost($this->host);
        $this->setDesiredCapabilities($this->capabilities);

        $this->setUpTraits();

        foreach ($this->afterApplicationCreatedCallbacks as $callback) {
            call_user_func($callback);
        }

        $this->setUpHasRun = true;
    }

    /**
     * Refresh the application instance.
     *
     * @return void
     */
    protected function refreshApplication()
    {
        $this->app = $this->createApplication();
    }

    /**
     * Boot the testing helper traits.
     *
     * @return void
     */
    protected function setUpTraits()
    {
        $uses = array_flip(class_uses_recursive(static::class));

        if (isset($uses[DatabaseMigrations::class])) {
            $this->runDatabaseMigrations();
        }
    }

    /**
     * Switches the application environment to local.
     */
    protected function tearDown()
    {
        if ($this->app) {
            foreach ($this->beforeApplicationDestroyedCallbacks as $callback) {
                call_user_func($callback);
            }
            $this->app->flush();

            $this->app = null;
        }

        $this->setUpHasRun = false;

        $this->afterApplicationCreatedCallbacks = [];
        $this->beforeApplicationDestroyedCallbacks = [];

        $this->switchEnvironment($this->localEnvFile);
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
            $error = new PHPUnit_Framework_Error("Screen shot for [{$this->url()}] saved to: {$fileName}",
                $e->getCode(), $e->getFile(), $e->getLine(), $e);
        } else {
            $error = $e;
        }

        parent::onNotSuccessfulTest($error);
    }

    /**
     * Register a callback to be run after the application is created.
     *
     * @param  callable  $callback
     * @return void
     */
    protected function afterApplicationCreated(callable $callback)
    {
        $this->afterApplicationCreatedCallbacks[] = $callback;

        if ($this->setUpHasRun) {
            call_user_func($callback);
        }
    }

    /**
     * Register a callback to be run before the application is destroyed.
     *
     * @param  callable  $callback
     * @return void
     */
    protected function beforeApplicationDestroyed(callable $callback)
    {
        $this->beforeApplicationDestroyedCallbacks[] = $callback;
    }

    /**
     * @return bool
     * @throws InvalidArgumentException|FileNotFoundException
     */
    protected function switchEnvironment($env)
    {
        if ($this->envFilesChecked) {
            return copy($env, $this->envFile);
        }
    }

    protected function checkEnvFiles()
    {
        if (is_null($this->envFile)) {
            throw new InvalidArgumentException("The path to the .env file must be defined.");
        }

        if (! file_exists($this->envFile)) {
            throw new FileNotFoundException("The env file [{$this->envFile}] couldn't be found.");
        }

        if (is_null($this->testingEnvFile)) {
            throw new InvalidArgumentException("The path to the testing environment file must be defined.");
        }

        if (! file_exists($this->testingEnvFile)) {
            throw new FileNotFoundException("The testing environment file [{$this->testingEnvFile}] couldn't be found.");
        }

        if (is_null($this->localEnvFile)) {
            throw new InvalidArgumentException("The path to the local environment file must be defined.");
        }

        if (! file_exists($this->localEnvFile)) {
            throw new FileNotFoundException("The local environment file [{$this->localEnvFile}] couldn't be found.");
        }

        $this->envFilesChecked = true;
    }
}
<?php

namespace Tests;

use Laravel\BrowserKitTesting\TestCase as BaseTestCase;
use App\Exceptions\Handler;
use Illuminate\Contracts\Debug\ExceptionHandler;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    protected $baseUrl = 'http://homestead.test';

    protected function disableExceptionHandling()
    {
        $this->oldExceptionHandler = $this->app->make(ExceptionHandler::class);
        $this->app->instance(ExceptionHandler::class, new class extends Handler {
            public function __construct() {}
            public function report(\Exception $e) {}
            public function render($request, \Exception $e) {
                throw $e;
            }
        });
    }

}

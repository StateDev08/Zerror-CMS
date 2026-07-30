<?php

namespace Tests;

use App\Support\Installer;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (! Installer::isInstalled()) {
            Installer::markInstalled();
        }
    }
}

<?php

namespace Tests\Unit\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * @internal
 */
final class HomeControllerTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    public function testHomePageRendersLoginForm(): void
    {
        $result = $this->get('/');

        $result->assertOK();
        $result->assertSee('Login');
    }
}

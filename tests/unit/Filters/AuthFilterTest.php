<?php

namespace Tests\Unit\Filters;

use App\Filters\AuthFilter;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FilterTestTrait;

/**
 * @internal
 */
final class AuthFilterTest extends CIUnitTestCase
{
    use FilterTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->filtersConfig = new \Config\Filters();
    }

    public function testAuthFilterRedirectsWhenNotLoggedIn(): void
    {
        $filter = new AuthFilter();

        $request = service('request');
        $result = $filter->before($request);

        $this->assertNotNull($result);
        $this->assertInstanceOf(\CodeIgniter\HTTP\RedirectResponse::class, $result);
    }

    public function testAuthFilterAllowsAccessWhenLoggedIn(): void
    {
        $session = session();
        $session->set('logged_in', true);

        $filter = new AuthFilter();
        $request = service('request');
        $result = $filter->before($request);

        $this->assertNull($result);

        $session->remove('logged_in');
    }

    public function testAfterMethodReturnsVoid(): void
    {
        $filter = new AuthFilter();
        $request = service('request');
        $response = service('response');

        $result = $filter->after($request, $response);

        $this->assertNull($result);
    }
}

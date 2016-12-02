<?php

namespace TestApp\Tests\Loader;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use TestApp\Loader\MagicLoader;
use TestApp\Tests\Loader\Fixture\SomeBundle\Controller\MyController;

/**
 * Class MagicLoaderTest
 * @package Tests\AppBundle\Controller
 */
class MagicLoaderTest extends WebTestCase
{
    /**
     * Magic loader test
     *
     * @return void
     *
     * @covers ::load
     */
    public function testMagicLoader()
    {
        $availableRoutes = [
            '/some/my/foo',
            '/some/my/bar',
        ];

        $magicLoader = (new MagicLoader());

        $this->assertInstanceOf(MagicLoader::class, $magicLoader, 'Incorrect class');

        $routeCollection = $magicLoader->load(MyController::class);

        $this->assertInstanceOf(RouteCollection::class, $routeCollection, 'Incorrect route collection');

        foreach ($routeCollection as $route) {
            $this->assertInstanceOf(Route::class, $route, 'Incorrect route internal type');

            $staticPrefix = $route
                ->compile()
                ->getStaticPrefix();

            $this->assertNotEmpty($staticPrefix, 'Prefix is empty');
            $this->assertContains($staticPrefix, $availableRoutes, 'Incorrect route');
        }
    }
}

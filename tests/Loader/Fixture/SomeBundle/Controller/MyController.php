<?php

namespace TestApp\Tests\Loader\Fixture\SomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MyController extends Controller
{
    /**
     * Foo action
     *
     * @param \Symfony\Component\HttpFoundation\Request $request Request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function fooAction(Request $request): Response
    {
        return new Response('foo');
    }

    /**
     * Bar action
     *
     * @param \Symfony\Component\HttpFoundation\Request $request Request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function barAction(Request $request)
    {
        return new Response('bar');
    }

    /**
     * Baz not action
     *
     * @return Response
     */
    protected function bazAction()
    {
        return new Response('baz');
    }

    /**
     * Baz not action
     *
     * @return Response
     */
    public function getActions(): Response
    {
        return new Response([
            'foo',
            'bar',
        ]);
    }
}

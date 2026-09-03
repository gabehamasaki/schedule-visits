<?php

namespace Tests\Unit\Infrastructure;

use App\Infrastructure\Http\Request;

class RequestTest
{
    public function testRequestCreation(): void
    {
        $request = new Request(
            'GET',
            '/test',
            ['id' => '123'],
            ['search' => 'query'],
            ['name' => 'John Doe']
        );

        assert($request->method === 'GET');
        assert($request->uri === '/test');
        assert($request->param('id') === '123');
        assert($request->query('search') === 'query');
        assert($request->input('name') === 'John Doe');
    }

    public function testRequestFromGlobals(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/submit';
        $_GET = ['search' => 'query'];
        $inputData = json_encode(['name' => 'John Doe']);
        file_put_contents('php://input', $inputData);

        $request = Request::fromGlobals();

        assert($request->method === 'POST');
        assert($request->uri === '/submit');
        assert($request->query('search') === 'query');
        assert($request->input('name') === 'John Doe');
    }
}

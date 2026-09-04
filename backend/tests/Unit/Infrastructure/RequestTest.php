<?php

namespace Tests\Unit\Infrastructure;

use App\Infrastructure\Http\Request;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
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

        $this->assertSame('GET', $request->method);
        $this->assertSame('/test', $request->uri);
        $this->assertSame('123', $request->param('id'));
        $this->assertSame('query', $request->query('search'));
        $this->assertSame('John Doe', $request->input('name'));
    }

    public function testRequestFromGlobalsReadsMethodUriAndQuery(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/submit';
        $_GET = ['search' => 'query'];

        $request = Request::fromGlobals();

        $this->assertSame('GET', $request->method);
        $this->assertSame('/submit', $request->uri);
        $this->assertSame('query', $request->query('search'));
    }
}

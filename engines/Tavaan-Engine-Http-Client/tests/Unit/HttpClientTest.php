<?php

declare(strict_types=1);

namespace Tavaan\Engine\HttpClient\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\HttpClient\HttpClient;
use Tavaan\Engine\HttpClient\PendingRequest;
use Tavaan\Engine\HttpClient\Response;
use Tavaan\Engine\HttpClient\Pool;
use Tavaan\Engine\HttpClient\Exceptions\HttpClientException;

/**
 * تست‌های Unit برای موتور HttpClient.
 *
 * @see docs/03-engines/http-client.md (DOC-064)
 */
final class HttpClientTest extends TestCase
{
    public function test_response_successful(): void
    {
        $r = new Response(200, 'ok');
        $this->assertTrue($r->successful());
        $this->assertFalse($r->failed());
    }

    public function test_response_client_error(): void
    {
        $r = new Response(404, 'not found');
        $this->assertTrue($r->failed());
        $this->assertTrue($r->clientError());
        $this->assertFalse($r->serverError());
    }

    public function test_response_server_error(): void
    {
        $r = new Response(500, 'error');
        $this->assertTrue($r->failed());
        $this->assertTrue($r->serverError());
    }

    public function test_response_json(): void
    {
        $r = new Response(200, '{"name":"Ali","age":25}');
        $data = $r->json();
        $this->assertSame('Ali', $data['name']);
        $this->assertSame(25, $data['age']);
    }

    public function test_response_json_with_key(): void
    {
        $r = new Response(200, '{"user":{"id":42}}');
        $this->assertSame(42, $r->json('user.id'));
        $this->assertNull($r->json('user.missing', 'default'));
    }

    public function test_response_json_invalid(): void
    {
        $r = new Response(200, 'not json');
        $this->assertNull($r->json());
    }

    public function test_response_header(): void
    {
        $r = new Response(200, 'body', ['Content-Type' => 'application/json']);
        $this->assertSame('application/json', $r->header('Content-Type'));
        $this->assertSame('application/json', $r->header('content-type'));  // case insensitive
        $this->assertNull($r->header('Missing'));
    }

    public function test_response_throw_on_failure(): void
    {
        $r = new Response(404, 'not found');
        $this->expectException(HttpClientException::class);
        $r->throw();
    }

    public function test_response_throw_passes_on_success(): void
    {
        $r = new Response(200, 'ok');
        $this->assertSame($r, $r->throw());
    }

    public function test_response_throw_if(): void
    {
        $r = new Response(200, 'ok');
        $r->throwIf(fn($resp) => $resp->status() === 429);
        $this->assertTrue(true);

        $r2 = new Response(429, 'rate limited');
        $this->expectException(HttpClientException::class);
        $r2->throwIf(fn($resp) => $resp->status() === 429);
    }

    public function test_pending_request_with_headers(): void
    {
        $req = new PendingRequest();
        $req->withHeaders(['Authorization' => 'Bearer xyz', 'Accept' => 'application/json']);
        $this->assertSame(['Authorization' => 'Bearer xyz', 'Accept' => 'application/json'], $req->getHeaders());
    }

    public function test_pending_request_with_header(): void
    {
        $req = new PendingRequest();
        $req->withHeader('X-Custom', 'value');
        $this->assertSame('value', $req->getHeaders()['X-Custom']);
    }

    public function test_pending_request_with_token(): void
    {
        $req = new PendingRequest();
        $req->withToken('xyz123');
        $this->assertSame('Bearer xyz123', $req->getHeaders()['Authorization']);
    }

    public function test_pending_request_with_token_custom_type(): void
    {
        $req = new PendingRequest();
        $req->withToken('xyz', 'Basic');
        $this->assertSame('Basic xyz', $req->getHeaders()['Authorization']);
    }

    public function test_pending_request_as_json(): void
    {
        $req = new PendingRequest();
        $req->asJson();
        $this->assertSame('application/json', $req->getHeaders()['Content-Type']);
        $this->assertSame('application/json', $req->getHeaders()['Accept']);
    }

    public function test_pending_request_as_form(): void
    {
        $req = new PendingRequest();
        $req->asForm();
        $this->assertSame('application/x-www-form-urlencoded', $req->getHeaders()['Content-Type']);
    }

    public function test_pending_request_accept_json(): void
    {
        $req = new PendingRequest();
        $req->acceptJson();
        $this->assertSame('application/json', $req->getHeaders()['Accept']);
    }

    public function test_pending_request_timeout(): void
    {
        $req = new PendingRequest();
        $req->timeout(60);
        $this->assertSame(60, $req->getTimeout());
    }

    public function test_pending_request_retry(): void
    {
        $req = new PendingRequest();
        $req->retry(3, 200);
        $this->assertSame(3, $req->getTries());
    }

    public function test_pending_request_query(): void
    {
        $req = new PendingRequest();
        $req->withQueryParameters(['page' => 2, 'limit' => 10]);
        $this->assertSame(['page' => '2', 'limit' => '10'], $req->getQuery());
    }

    public function test_http_client_new(): void
    {
        $c = HttpClient::new();
        $this->assertInstanceOf(HttpClient::class, $c);
    }

    public function test_http_client_request(): void
    {
        $c = new HttpClient(['timeout' => 45]);
        $req = $c->request();
        $this->assertInstanceOf(PendingRequest::class, $req);
        $this->assertSame(45, $req->getTimeout());
    }

    public function test_http_client_default_headers_from_config(): void
    {
        $c = new HttpClient(['default_headers' => ['User-Agent' => 'Tavaan']]);
        $req = $c->request();
        $this->assertSame('Tavaan', $req->getHeaders()['User-Agent']);
    }

    public function test_pool_get(): void
    {
        $pool = new Pool();
        $pool->as('user')->get('http://nonexistent.example.com/user');
        $pool->as('posts')->get('http://nonexistent.example.com/posts');
        $results = $pool->execute();
        $this->assertArrayHasKey('user', $results);
        $this->assertArrayHasKey('posts', $results);
    }

    public function test_pool_post(): void
    {
        $pool = new Pool();
        $pool->as('create')->post('http://nonexistent.example.com/create', ['name' => 'test']);
        $results = $pool->execute();
        // Should not throw
        $this->assertContainsOnlyInstancesOf(Response::class, $results);
    }
}

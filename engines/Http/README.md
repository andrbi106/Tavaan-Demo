# Http

> Request/Response Immutable، Kernel + Middleware Pipeline، و Response Factory.

| فیلد | مقدار |
|------|------|
| Engine ID | `tavaan.engine.http` |
| DOC-ID | `DOC-039` |
| Namespace | `Tavaan\Engine\Http` |
| اولویت | P0 (فاز M2) |
| Facade | `Http` (accessor: `http`) |
| مخزن | https://github.com/andrbi106/Tavaan-Engine-Http |
| مستندات | https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/http.md

## استفاده

```php
use Tavaan\Engine\Http\Request;
use Tavaan\Engine\Http\Response;
use Tavaan\Engine\Http\HttpKernel;

// Request
$req = Request::create('/users?name=Ali', 'GET');
$req->getMethod();      // 'GET'
$req->getPath();        // '/users'
$req->query->get('name'); // 'Ali'
$req->input('name');    // 'Ali'
$req->header('Accept');  // null
$req->all();             // ['name' => 'Ali']

// POST با JSON
$req = Request::create('/', 'POST', [], [], '{"user":{"id":42}}');
$req->json('user.id');  // 42

// از محیط واقعی
$req = Request::capture();

// Response
$res = Response::json(['status' => 'ok']);
$res = Response::text('Hello');
$res = Response::html('<h1>Hi</h1>');
$res = Response::redirect('/new-path');
$res = Response::notFound('Not Found');
$res = Response::noContent();
$res->setHeader('X-Custom', 'value');
$res->send();

// Kernel
$kernel = new HttpKernel();
$kernel->setDispatcher(fn(Request $r) => Response::text('Hello from ' . $r->getPath()));
$kernel->addMiddleware(fn(Request $r) => null);  // pass-through
$kernel->addMiddleware(fn(Request $r) => Response::text('blocked'));  // short-circuit
$response = $kernel->handle($req);
$kernel->terminate($req, $response);

// اجرا و send خودکار
$kernel->run();
```

## مستندات

[docs/03-engines/http.md](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/http.md)

---
*Generated on 2026-11-03.*

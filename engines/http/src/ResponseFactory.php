<?php

declare(strict_types=1);

namespace Tavaan\Engine\Http;

final class ResponseFactory
{
    public function json(mixed $data = null, int $status = 200, array $headers = []): Response
    {
        $content = json_encode($data ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return new Response($status, $content ?: '{}', array_merge(['Content-Type' => 'application/json; charset=utf-8'], $headers));
    }

    public function view(string $view, array $data = [], int $status = 200, array $headers = []): Response
    {
        return new Response($status, '<!-- View: ' . $view . ' -->', array_merge(['Content-Type' => 'text/html; charset=utf-8'], $headers));
    }

    public function file(string $path, array $headers = []): Response
    {
        if (!file_exists($path)) return new Response(404, 'File not found');
        $content = file_get_contents($path) ?: '';
        $mime = function_exists('mime_content_type') ? mime_content_type($path) : 'application/octet-stream';
        return new Response(200, $content, array_merge(['Content-Type' => $mime, 'Content-Disposition' => 'inline'], $headers));
    }

    public function download(string $path, ?string $name = null, array $headers = []): Response
    {
        if (!file_exists($path)) return new Response(404, 'File not found');
        $content = file_get_contents($path) ?: '';
        $filename = $name ?? basename($path);
        return new Response(200, $content, array_merge([
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ], $headers));
    }

    public function stream($resource, int $status = 200, array $headers = []): Response
    {
        $content = '';
        if (is_resource($resource)) {
            while (!feof($resource)) { $content .= fread($resource, 8192); }
        }
        return new Response($status, $content, $headers);
    }

    public function streamDownload($resource, ?string $name = null, array $headers = []): Response
    {
        $content = '';
        if (is_resource($resource)) {
            while (!feof($resource)) { $content .= fread($resource, 8192); }
        }
        $filename = $name ?? 'download';
        return new Response(200, $content, array_merge([
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ], $headers));
    }

    public function noContent(int $status = 204, array $headers = []): Response
    {
        return new Response($status, '', $headers);
    }

    public function make(int $status, string $content = '', array $headers = []): Response
    {
        return new Response($status, $content, $headers);
    }

    public function redirectToRoute(string $route, array $params = [], int $status = 302, array $headers = []): Response
    {
        return new Response($status, '', array_merge(['Location' => $route], $headers));
    }

    public function redirectToUrl(string $url, int $status = 302, array $headers = []): Response
    {
        return new Response($status, '', array_merge(['Location' => $url], $headers));
    }

    public function problem(string $title, int $status = 400, string $detail = '', string $type = 'about:blank'): Response
    {
        $body = json_encode(['type' => $type, 'title' => $title, 'status' => $status, 'detail' => $detail], JSON_UNESCAPED_UNICODE);
        return new Response($status, $body ?: '{}', ['Content-Type' => 'application/problem+json; charset=utf-8']);
    }
}

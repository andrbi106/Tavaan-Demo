<?php

declare(strict_types=1);

namespace Tavaan\Engine\Scaffold;

final class ScaffoldBuilder
{
    private array $templates = [];

    public function __construct(private readonly array $config = [])
    {
        $this->templates = $config['templates'] ?? [
            'model' => "<?php\n\ndeclare(strict_types=1);\n\nnamespace App\\Models;\n\nfinal class {{Class}}\n{\n    protected string \$table = '{{table}}';\n}\n",
            'controller' => "<?php\n\ndeclare(strict_types=1);\n\nnamespace App\\Http\\Controllers;\n\nfinal class {{Class}}\n{\n    public function index()\n    {\n        return response()->json(['data' => []]);\n    }\n}\n",
            'migration' => "<?php\n\ndeclare(strict_types=1);\n\nuse Tavaan\\Engine\\Database\\Migration\\Migration;\n\nreturn new class extends Migration\n{\n    public string \$version = '1.0.0';\n    public string \$description = 'Create {{table}} table';\n\n    public function up(): void\n    {\n        // Schema::create('{{table}}', ...);\n    }\n};\n",
            'test' => "<?php\n\ndeclare(strict_types=1);\n\nnamespace App\\Tests\\Unit;\n\nuse PHPUnit\\Framework\\TestCase;\n\nfinal class {{Class}}Test extends TestCase\n{\n    public function testBasic(): void\n    {\n        \$this->assertTrue(true);\n    }\n}\n",
        ];
    }

    public function generate(string $type, string $name, array $params = []): string
    {
        $template = $this->templates[$type] ?? '';
        $table = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name));
        $replacements = array_merge(['{{Class}}' => $name, '{{table}}' => $table], $params);
        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }

    public function save(string $type, string $name, string $directory, array $params = []): string
    {
        $content = $this->generate($type, $name, $params);
        $filename = match($type) {
            'migration' => date('Y_m_d_His') . '_create_' . strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name)) . '_table.php',
            'test' => $name . 'Test.php',
            default => $name . '.php',
        };
        $path = rtrim($directory, '/') . '/' . $filename;
        if (!is_dir(dirname($path))) mkdir(dirname($path), 0777, true);
        file_put_contents($path, $content);
        return $path;
    }

    public function scaffold(string $name, string $directory, array $params = []): array
    {
        return [
            $this->save('model', $name, $directory . '/Models', $params),
            $this->save('controller', $name . 'Controller', $directory . '/Http/Controllers', $params),
            $this->save('migration', $name, $directory . '/migrations', $params),
            $this->save('test', $name, $directory . '/Tests/Unit', $params),
        ];
    }

    public function getTemplate(string $type): string { return $this->templates[$type] ?? ''; }
    public function setTemplate(string $type, string $template): void { $this->templates[$type] = $template; }
    public function getTemplateTypes(): array { return array_keys($this->templates); }
}

final class ScaffoldServiceProvider
{
    private ?ScaffoldBuilder $builder = null;
    public function __construct(private readonly array $config = []) {}
    public function register(): void { $this->builder = new ScaffoldBuilder($this->config); }
    public function boot(): void {}
    public function provides(): array { return [ScaffoldBuilder::class]; }
    public function getBuilder(): ScaffoldBuilder { if ($this->builder === null) $this->register(); return $this->builder; }
}

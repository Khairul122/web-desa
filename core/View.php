<?php

namespace App\Core;

use Exception;

class View
{
    private static $layout;
    private static $sharedData = [];
    private static $sections = [];
    private static $currentSection = null;

    public static function make(string $view, array $data = []): string
    {
        $file = APP_PATH . '/Views/' . str_replace('.', '/', $view) . '.php';
        
        if (!file_exists($file)) {
            throw new Exception("View file not found: $file");
        }

        extract(array_merge(self::$sharedData, $data));
        
        ob_start();
        include $file;
        return ob_get_clean();
    }

    public static function render(string $view, array $data = []): void
    {
        echo self::make($view, $data);
    }

    public static function layout(string $layout): self
    {
        self::$layout = $layout;
        return new self();
    }

    public static function share(array $data): void
    {
        self::$sharedData = array_merge(self::$sharedData, $data);
    }

    public static function section(string $name): void
    {
        self::$currentSection = $name;
        ob_start();
    }

    public static function endsection(): void
    {
        if (self::$currentSection !== null) {
            self::$sections[self::$currentSection] = ob_get_clean();
            self::$currentSection = null;
        }
    }

    public static function yield(string $name): string
    {
        return self::$sections[$name] ?? '';
    }

    public static function parentSection(string $name): void
    {
        echo self::$sections[$name] ?? '';
    }

    public static function include(string $view, array $data = []): void
    {
        echo self::make($view, $data);
    }

    public static function component(string $component, array $data = []): void
    {
        $componentPath = APP_PATH . '/Views/components/' . str_replace('.', '/', $component) . '.php';
        
        if (!file_exists($componentPath)) {
            throw new Exception("Component not found: $componentPath");
        }

        extract(array_merge(self::$sharedData, $data));
        include $componentPath;
    }

    public static function start(): void
    {
        ob_start();
    }

    public static function stop(): string
    {
        return ob_get_clean();
    }

    public static function flush(): void
    {
        ob_end_flush();
    }

    public static function getLayout(): ?string
    {
        return self::$layout;
    }

    public static function renderWithLayout(string $view, array $data = [], string $layout = null): string
    {
        $content = self::make($view, $data);
        $layout = $layout ?? self::$layout;
        
        if (!$layout) {
            return $content;
        }

        return self::make($layout, array_merge($data, ['content' => $content]));
    }
}

class Layout
{
    public static function begin(string $layout): void
    {
        View::share(['_content' => '']);
        ob_start();
    }

    public static function end(): string
    {
        $content = ob_get_clean();
        $layout = View::getLayout();
        return View::renderWithLayout($layout, ['content' => $content]);
    }
}

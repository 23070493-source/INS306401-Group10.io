<?php

class Controller
{
    protected function view(string $view, array $data = []): void
    {
        render($view, $data);
    }

    protected function redirect(string $route): void
    {
        redirectTo($route);
    }
}

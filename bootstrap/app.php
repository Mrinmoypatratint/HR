<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');
        $middleware->redirectGuestsTo(function (\Illuminate\Http\Request $request) {
            if ($request->is('employee') || $request->is('employee/*')) {
                return route('employee.login');
            }
            return route('admin.login');
        });
        $middleware->validateCsrfTokens(except: [
            'employee/logout',
            'admin/logout',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            if ($request->is('employee/logout') || $request->is('admin/logout')) {
                return redirect()->route('employee.portal')->with('info', 'You have been safely signed out.');
            }
        });
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, \Illuminate\Http\Request $request) {
            if ($e->getStatusCode() === 419 && ($request->is('employee/logout') || $request->is('admin/logout'))) {
                return redirect()->route('employee.portal')->with('info', 'You have been safely signed out.');
            }
        });
    })->create();

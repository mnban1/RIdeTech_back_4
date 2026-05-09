<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * ロールベースのアクセス制御ミドルウェア
 *
 * 使用例: Route::middleware('role:shop_admin')->group(...)
 *
 * bootstrap/app.php で登録:
 *   ->withMiddleware(function (Middleware $middleware) {
 *       $middleware->alias(['role' => CheckRole::class]);
 *   })
 */
class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user || !in_array($user->role, $roles)) {
            abort(403, 'このページにアクセスする権限がありません。');
        }

        return $next($request);
    }
}

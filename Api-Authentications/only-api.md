
  ***  if its api only otherwise optional ***
  
```php
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            return $request->is('api/*') || $request->expectsJson();
        });

        $exceptions->render(function (Throwable $e, Request $request) {
            if ($e instanceof AuthenticationException) {
                return response()->json([
                    'status' => 0,
                    'message' => $e->getMessage(),
                ], Response::HTTP_UNAUTHORIZED);
            }

            if ($e instanceof NotFoundHttpException) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Route not found.',
                ], Response::HTTP_NOT_FOUND);
            }

            if ($e instanceof MethodNotAllowedHttpException) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Method not allowed.',
                ], Response::HTTP_METHOD_NOT_ALLOWED);
            }

            return null; // default Laravel error handling for other cases
        });
    })
    ->create();

```

---
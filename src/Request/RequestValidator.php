<?php

namespace App\Request;

use Symfony\Component\HttpFoundation\Request;

class RequestValidator
{
    public function validate(
        Request $request,
        ?array $expectedBody = [],
        ?array $expectedHeaders = []
    ): ?array {
        $body = [];
        $headers = $request->headers->all();

        if ($request->headers->get('Content-Type') === 'application/json') {
            $body = json_decode($request->getContent(), true);
        }

        foreach ($expectedBody as $field) {
            if (!array_key_exists($field, $body)) {
                return null;
            }
        }

        foreach ($expectedHeaders as $field) {
            if (!array_key_exists($field, $headers)) {
                return null;
            }
        }

        return $body;
    }
}

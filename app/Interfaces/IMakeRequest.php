<?php

namespace App\Abstracts;

interface IMakeRequest
{
    public function getAsJson(string $url, array $headers=[]);
    public function postAsJson(string $url,array $body=[], array $headers=[]);
    public function postAsForm(string $url,array $body = [], array $headers=[]);
}

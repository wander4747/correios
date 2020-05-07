<?php

namespace Correios\Request;

interface RequestInterface
{
    
    public function get(string $url, array $query, array $options = []);

    public function post(string $url, array $body, array $options = []);
}
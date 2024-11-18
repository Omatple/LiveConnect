<?php

namespace MyApp\utils;

enum Role: string
{
    case Root = "root";
    case Admin = "admin";
    case User = "user";
    case Guest = "guest";

    public function toString(): string
    {
        return $this->value;
    }
}

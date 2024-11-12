<?php

namespace MyApp\db;

enum Role: string
{
    case Root = "root";
    case Admin = "admin";
    case Mod = "moderator";
    case Premium = "premium";
    case User = "user";
    case Guest = "guest";
    case Banned = "banned";

    public function toString(): string
    {
        return $this->value;
    }
}

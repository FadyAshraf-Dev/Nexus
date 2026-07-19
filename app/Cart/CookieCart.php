<?php

declare(strict_types=1);

final class CookieCart
{
    public function read(): array
    {
        $cookie = Cookie::get(Config::app('cookies.cart.name'));

        if ($cookie === null) {
            return [];
        }

        $cart = json_decode($cookie, true);

        return is_array($cart)
            ? $cart
            : [];
    }

    public function write(array $cart): void
    {
        Cookie::set(
            Config::app('cookies.cart.name'),
            json_encode($cart, JSON_THROW_ON_ERROR),
            strtotime(Config::app('cookies.cart.duration'))
        );
    }

    public function delete(): void
    {
        Cookie::delete(Config::app('cookies.cart.name'));
    }

    public function exists(): bool
    {
        return Cookie::exists(Config::app('cookies.cart.name'));
    }

    public function isEmpty(): bool
    {
        return $this->read() === [];
    }
}
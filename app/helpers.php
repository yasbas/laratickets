<?php


/*
    Some helper for requesting a hashed ids from the db. E.g.:

    $ticket = Ticket::where(hashDbId(), $ticketHashedId)->first();

    This is like the following in MySql:

    SELECT * FROM tickets WHERE sha1( concat('someString', id)) = sha1( concat('someString', '228'))
 */

if (! function_exists( 'hashStr' )) {
    function hashStr($str)
    {
        return sha1(env('APP_KEY', 'kl23jlkqwj0120-askd9(fgh3$@zx.124,') . $str);
    }
}

if (! function_exists( 'hashDbId' )) {
    function hashDbId()
    {
        return DB::raw("sha1( concat('base64:5lDsUEsaJGBAvxluMLvvagKcu2KxHXXW7+MVzCu2fjA=', id))");
    }
}

<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Private channel for office visit notifications: user.{id} (assignee or reception)
Broadcast::channel('user.{id}', function ($user, $id) {
    $admin = auth('admin')->user();
    return $admin && (int) $admin->id === (int) $id;
});

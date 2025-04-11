<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('public-chat', function () {
    return true;
});

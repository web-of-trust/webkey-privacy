<?php

use App\Enums\Panel;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect(env('USER_PANEL_PATH', Panel::User->path()));
});

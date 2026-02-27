<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Media\Controllers\MediaController;

Route::post('/upload', [MediaController::class, 'upload'])
    ->name('media.upload');
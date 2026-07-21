<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Chat\ChatBot;

Route::view('/', 'welcome')->name('home');

Route::livewire('/chat', ChatBot::class)->name('chat');
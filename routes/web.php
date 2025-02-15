<?php

use Illuminate\Support\Facades\Route;
use App\Events\WebsocketDemoEvent;
use App\Http\Controllers\Chats;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    // broadcast(new WebsocketDemoEvent('hello world'));
    return view('welcome');
});

Route::get('/chats', [Chats::class, 'index'])->name('user.chats');

Route::get('/messages/{id}', [Chats::class, 'getMessages'])->name('user.get-messages');

Route::post('/messages/{id}', [Chats::class, 'sendMessages'])->name('user.send-messages');


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');


// Route::get('/chats');
require __DIR__.'/auth.php';

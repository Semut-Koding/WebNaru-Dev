<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\PublicController;

Route::get('/', [PublicController::class, 'index'])->name('home');
Route::get('/about', [PublicController::class, 'about'])->name('about');
Route::get('/attractions', [PublicController::class, 'attractions'])->name('attractions');
Route::get('/attractions/{id}', [PublicController::class, 'attractionDetail'])->name('attractions.detail');
Route::get('/villas', [PublicController::class, 'villas'])->name('villas');
Route::get('/villas/{id}', [PublicController::class, 'villaDetail'])->name('villas.detail');
Route::get('/pricing', [PublicController::class, 'pricing'])->name('pricing');
Route::get('/faq', [PublicController::class, 'faq'])->name('faq');
Route::get('/contact', [PublicController::class, 'contact'])->name('contact');
Route::get('/gallery', [PublicController::class, 'gallery'])->name('gallery');

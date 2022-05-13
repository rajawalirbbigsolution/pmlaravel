<?php 
Route::get('/', 'a1@a')->name('home');
Route::post('buatuser', 'a1@b'); 
Route::get('login', 'AuthC@L')->name('login');
Route::get('register', 'a@a');
Route::post('register/baru', 'a@b');  
Route::get('logout', 'AuthC@c')->name('logout');
Route::post('login', 'AuthC@Lp'); 

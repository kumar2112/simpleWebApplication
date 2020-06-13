<?php

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

// Route::get('/', function () {
//     return view('log');
// });
//Routers Routes

Route::get('/', 'HomeController@index')->name('home');

//Route::get('/routers', 'RouterController@listEmployee')->name('router.list');
//Route::match(array('GET','POST'),'router.list','RouterController@listEmployee');

Route::match(['get','post'],'/routers', array('as' => 'router.list', 'uses' => 'RouterController@listEmployee'));

Route::get('/routers/create', 'RouterController@createRouter')->name('router.create');


Route::post('/routers/store', 'RouterController@storeRouter')->name('router.store');

Route::get('/routers/edit/{id}', 'RouterController@editRouter')->where(['id' => '[A-Za-z0-9]+'])->name('router.edit');


Route::post('/routers/update', 'RouterController@updateRouter')->name('router.update');


Route::get('/routers/delete/{id}', 'RouterController@deleteRouter')->where(['id' => '[A-Za-z0-9]+'])->name('router.delete');

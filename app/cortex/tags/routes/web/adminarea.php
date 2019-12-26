<?php

declare(strict_types=1);

Route::domain(domain())->group(function () {
    Route::name('adminarea.')
         ->namespace('Cortex\Tags\Http\Controllers\Adminarea')
         ->middleware(['web', 'nohttpcache', 'can:access-adminarea'])
         ->prefix(config('cortex.foundation.route.locale_prefix') ? '{locale}/'.config('cortex.foundation.route.prefix.adminarea') : config('cortex.foundation.route.prefix.adminarea'))->group(function () {

        // Tags Routes
             Route::name('tags.')->prefix('tags')->group(function () {
                 Route::get('/')->name('index')->uses('TagsController@index');
                 Route::get('import')->name('import')->uses('TagsController@import');
                 Route::post('import')->name('stash')->uses('TagsController@stash');
                 Route::post('hoard')->name('hoard')->uses('TagsController@hoard');
                 Route::get('import/logs')->name('import.logs')->uses('TagsController@importLogs');
                 Route::get('create')->name('create')->uses('TagsController@form');
                 Route::post('create')->name('store')->uses('TagsController@store');
                 Route::get('{tag}')->name('show')->uses('TagsController@show');
                 Route::get('{tag}/edit')->name('edit')->uses('TagsController@form');
                 Route::put('{tag}/edit')->name('update')->uses('TagsController@update');
                 Route::get('{tag}/logs')->name('logs')->uses('TagsController@logs');
                 Route::delete('{tag}')->name('destroy')->uses('TagsController@destroy');
             });
         });
});

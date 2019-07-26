<?php

Route::group(['domain' => env('BASE_APP_URL')], function () {
    Route::get('/', 'Home\IndexController@homeViewContent');
    Route::group(['middleware' => 'cacheable:15'], function () {
        Route::get('p/{year}/{month}/{slug}', ['as' => "post", 'uses' => 'HomeController@mblogContent']);
        Route::get('/search/result','Web\SearchController@viewSearchResult');
        Route::group([], function () {
            Route::get('/{year}/{month}/{slug}', 'Web\IndexController@singlePostContent')->name('get.singlePost'); # Get Single Post
        });
        Route::group(['prefix' => 'comment'], function () {
            Route::post('/save', 'Comment\IndexController@saveComment')->name('save.comment'); # Save Comment
            Route::post('/likes','Comment\IndexController@voteLikes');
            Route::post('/dislikes','Comment\IndexController@voteDislikes');
        });
    });
});

Route::group(['domain' => "m.lindaikejisblog.com"], function () {
    Route::get('/', 'HomeController@mobilehome');
    Route::group(['middleware' => 'cacheable:15'], function () {
        Route::get('/search/result','Web\SearchController@viewSearchResult');
        // Route::get('p/{year}/{month}/{slug}', ['as' => "post", 'uses' => 'HomeController@mblogContent']);
        // Route::group([], function () {
        //     Route::get('/{year}/{month}/{slug}', ['as' => "post", 'uses' => 'HomeController@mblogContent']);
        // });
    });
});

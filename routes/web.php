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

Route::get('/jobmatchjob','RecommendationController@matchData');
Route::get('/matchsearch','RecommendationController@searchMatching');
Route::get('/explain','RecommendationController@explain');
Route::get('/logout','RecommendationController@logout');

//Crawl DATA

Route::get('/read-data', 'CrawlController@readDataFromDB');
Route::get('/create-index','CrawlController@createIndex');
Route::get('/clear-data-index','CrawlController@clearDataInIndex');
Route::get('/setting-index','CrawlController@putSettingIndex');
Route::get('/mapping-index','CrawlController@putMappingIndex');
Route::get('/update-setting-index','CrawlController@updateSettingIndex');
Route::get('/get-setting-index','CrawlController@getSettingIndex');



Route::get('/crawl-job', 'CrawlController@crawlJob');
Route::get('/crawl-CV','CrawlController@crawlCV');


Route::get('/term', 'CrawlController@termVector');
Route::get('/getdata','RecommendationController@getData');


// User Interface
Route::get('/login','UserInteractController@login')->name('login');
Route::get('/register','UserInteractController@register')->name('register');
Route::post('/login','UserInteractController@postLogin')->name('loginPost');
Route::group(['middleware'=>['web','auth']],function(){
    Route::get('/','UserInteractController@homepage')->name('homepage');
    Route::get('/profile_cv','UserInteractController@profileCV')->name('profile_cv');
    Route::get('/profile_job','UserInteractController@profileJob')->name('profile_job');

    Route::get('/search_cv','UserInteractController@searchCV')->name('search_cv');
    Route::get('/search_job','UserInteractController@searchJob')->name('search_job');

    Route::get('/cv/{id}','UserInteractController@interactCV')->name("info_cv");
    Route::get('/job/{id}','UserInteractController@interactJob')->name("info_job");

    Route::get('/recommend-today','UserInteractController@recommendToday')->name('recommendToday');

});


Route::get('/test','CrawlController@test');
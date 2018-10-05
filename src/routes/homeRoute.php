<?php

/**
 * home:主页数据
 */

$app->post('/ng2LifeStyle/login', 'Home:login');
$app->post('/ng2LifeStyle/logOut', 'Home:logOut');
$app->get('/home/homePopFilm', 'Home:homePopFilm');
$app->get('/home/homePopMusic', 'Home:homePopMusic');
$app->get('/home/homePopGame', 'Home:homePopGame');
$app->post('/home/popFilmLockOrFav', 'Home:popFilmLockOrFav');
$app->post('/home/popMusicFav', 'Home:popMusicFav');
$app->post('/home/gameFavOrLock', 'Home:gameFavOrLock');
$app->get('/film/filmDetail', 'Home:filmDetail');
$app->get('/film/getFilmTalk', 'Home:getFilmTalk');
$app->get('/home/homeDetailFilter', 'Home:homeDetailFilter');
$app->post('/film/doFilmTalk', 'Home:doFilmTalk');
$app->post('/film/doFilmScore', 'Home:doFilmScore');
<?php

/**
 * home:主页数据
 */
use \Src\Common\Util as Utils;
use \Firebase\JWT\JWT;

$app->post('/ng2LifeStyle/login', function ($request, $response) {
  $bodyParams = $request->getParsedBody();
  $username = $bodyParams['username'];
  $password = $bodyParams['password'];
  $sql = 'SELECT * FROM users WHERE user_id = :user_id';
  $sth = $this->db->prepare($sql);
  $sth->execute(array(':user_id' => $username));
  $result = $sth->fetchAll();

  if ($result[0]['user_id'] == $username) {
    $nowtime = time();
    $token = [
      'iss' => 'www.whatproblem.top',
      'aud' => 'user',
      'iat' => $nowtime,
      'exp' => $nowtime + 6000,
      'data' => [
        'userid' => 1,
        'username' => $username
      ]
    ];
    $jwt = JWT::encode($token, KEY);
    $res = ['code' => 200, 'msg' => 'login successfully!', 'data' => ['token' => $jwt]];
  } else {
    $res = ['msg' => 'login failed!'];
  }
  return $this->response->withJson($res);
});

$app->post('/ng2LifeStyle/logOut', function ($request, $response) {
  $jwt = $request->getHeaderLine('Authorization');
  $res = Utils::dealJwt($request, $jwt);
  if ($res['statusCode'] == '4') {
    $resp = ['msg' => 'logOut successfully!', 'code' => 200];
  } else if ($res['statusCode'] == '2') {
    $resp = ['msg' => 'login timeout!', 'code' => 201];
  } else {
    $resp = ['msg' => 'system error!', 'code' => 204];
  }
  return $this->response->withJson($resp);
});

$app->get('/home/homePopFilm', function ($request, $response) {
  $jwt = $request->getHeaderLine('Authorization');
  $queryParams = $request->getQueryParams();
  $film_score = $queryParams['film_score'];
  $sql = 'SELECT * FROM films WHERE film_score >= :film_score';
  $sth = $this->db->prepare($sql);
  $sth->execute(array(':film_score' => $film_score));
  $result = $sth->fetchAll();
  $utils = new Utils();
  $res = $utils->dealPic($result);
  
  // 判断是否登录
  if ($jwt != '') {
    $resLogin = Utils::dealJwt($request, $jwt);
    if ($resLogin['statusCode'] == '4') {
      $resFav = Utils::showLockOrFav($this, $resLogin, 'filmfav', $res, 'film_favorite', 'film_id');
      $resLock = Utils::showLockOrFav($this, $resLogin, 'filmlock', $resFav, 'film_lock', 'film_id');
    }
    $res = $resLock;
  }
  $resp = ['msg' => 'successfully!', 'data' => ['data' => $res], 'code' => 200];

  $utils = null;
  return $this->response->withJson($resp);
});

$app->get('/home/homePopMusic', function ($request, $response) {
  $jwt = $request->getHeaderLine('Authorization');
  $queryParams = $request->getQueryParams();
  $music_score = $queryParams['music_score'];
  $sql = 'SELECT * FROM musics WHERE music_score >= :music_score';
  $sth = $this->db->prepare($sql);
  $sth->execute(array(':music_score' => $music_score));
  $result = $sth->fetchAll();
  $utils = new Utils();
  $res = $utils->dealPic($result);
  
  // 判断登录
  if ($jwt != '') {
    $resLogin = Utils::dealJwt($request, $jwt);
    if ($resLogin['statusCode'] == '4') {
      $resFav = Utils::showLockOrFav($this, $resLogin, 'musicfav', $res, 'music_favorite', 'music_id');
    }
    $res = $resFav;
  }
  $resp = ['msg' => 'successfully!', 'data' => ['data' => $res], 'code' => 200];
  $utils = null;
  return $this->response->withJson($resp);
});

$app->get('/home/homePopGame', function ($request, $response) {
  $queryParams = $request->getQueryParams();
  $game_power = $queryParams['game_power'];
  $sql = 'SELECT * FROM games WHERE game_power >= :game_power';
  $sth = $this->db->prepare($sql);
  $sth->execute(array(':game_power' => $game_power));
  $result = $sth->fetchAll();
  $utils = new Utils();
  $res = $utils->dealPic($result);
  $resp = ['msg' => 'successfully!', 'data' => ['data' => $res], 'code' => 200];
  $utils = null;
  return $this->response->withJson($resp);
});










// $app->get('/', function ($request, $response) {
//   $sql = 'SELECT * FROM user';
//   $sth = $this->db->prepare($sql);
//   $sth->execute();
//   $res = $sth->fetchAll();
//   // $uri = $request->getUri();
//   // $res = $uri->getBaseUrl();
//   return $this->response->withJson($res);
//   // $urls = '../assets/image/home/ai_ke.jpg';
//   // $img = file_get_contents($urls, true);
//   // header('Content-type:image/jpeg;text/html; charset=utf-8');
//   // echo $img;
//   // exit;
// });

$app->get('/testGet', function ($request, $response) {
  $queryParams = $request->getQueryParams();
  $name = $queryParams['name'];
  $age = $queryParams['age'];
  $sql = 'SELECT * FROM user WHERE name = :name AND age = :age';
  $sth = $this->db->prepare($sql);
  $sth->execute(array(':name' => $name, ':age' => $age));
  $res = $sth->fetchAll();
  return $this->response->withJson($res);
});

$app->post('/', function ($request, $response) {
  $sql = 'SELECT * FROM user WHERE name = :name AND age = :age';
  $sth = $this->db->prepare($sql);
  $sth->execute(array(':name' => '小王', ':age' => '21'));
  $res = $sth->fetchAll();
  return $this->response->withJson($res);
});

$app->post('/testPost', function ($request, $response) {
  $bodyParams = $request->getParsedBody();
  $name = $bodyParams['name'];
  $age = $bodyParams['age'];
  $sql = 'SELECT * FROM user WHERE name = :name AND age = :age';
  $sth = $this->db->prepare($sql);
  $sth->execute(array(':name' => $name, ':age' => $age));
  $res = $sth->fetchAll();
  return $this->response->withJson($res);
});
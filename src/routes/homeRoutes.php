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
      $res = $resLock;
    }
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
      $res = $resFav;
    }
  }
  $resp = ['msg' => 'successfully!', 'data' => ['data' => $res], 'code' => 200];
  $utils = null;
  return $this->response->withJson($resp);
});

$app->get('/home/homePopGame', function ($request, $response) {
  $jwt = $request->getHeaderLine('Authorization');
  $queryParams = $request->getQueryParams();
  $game_power = $queryParams['game_power'];
  $sql = 'SELECT * FROM games WHERE game_power >= :game_power';
  $sth = $this->db->prepare($sql);
  $sth->execute(array(':game_power' => $game_power));
  $result = $sth->fetchAll();
  $utils = new Utils();
  $res = $utils->dealPic($result);

  // 判断登录
  if ($jwt != '') {
    $resLogin = Utils::dealJwt($request, $jwt);
    if ($resLogin['statusCode'] == '4') {
      $resFav = Utils::showLockOrFav($this, $resLogin, 'gamefav', $res, 'game_favorite', 'game_id');
      $resLock = Utils::showLockOrFav($this, $resLogin, 'gamelock', $resFav, 'game_lock', 'game_id');
      $res = $resLock;
    }
  }
  $resp = ['msg' => 'successfully!', 'data' => ['data' => $res], 'code' => 200];
  $utils = null;
  return $this->response->withJson($resp);
});

$app->post('/home/popFilmLockOrFav', function ($request, $response) {
  $jwt = $request->getHeaderLine('Authorization');
  $bodyParams = $request->getParsedBody();
  $film_id = isset($bodyParams['film_id']) ? $bodyParams['film_id'] : null;
  $film_lock = isset($bodyParams['film_lock']) ? $bodyParams['film_lock'] : null;
  $film_favorite = isset($bodyParams['film_favorite']) ? $bodyParams['film_favorite'] : null;

  if ($jwt != '') {
    $resLogin = Utils::dealJwt($request, $jwt);
    if ($resLogin['statusCode'] == '4') {
      $res = Utils::doFavOrLock($resLogin, $film_id, $film_lock, $film_favorite, $this, 'film');
      if ($res['statusCode'] = '1') {
        $resp = ['msg' => 'successfully!', 'code' => 200];
      } else {
        $resp = ['msg' => 'failed', 'code' => 201];
      }
    }
  } else {
    $resp = ['msg' => '请先登录', 'code' => 511];
  }
  return $this->response->withJson($resp);
});

$app->post('/home/popMusicFav', function ($request, $response) {
  $jwt = $request->getHeaderLine('Authorization');
  $bodyParams = $request->getParsedBody();
  $music_id = isset($bodyParams['music_id']) ? $bodyParams['music_id'] : null;
  $music_favorite = isset($bodyParams['music_favorite']) ? $bodyParams['music_favorite'] : null;

  if ($jwt != '') {
    $resLogin = Utils::dealJwt($request, $jwt);
    if ($resLogin['statusCode'] == '4') {
      $res = Utils::doFavOrLock($resLogin, $music_id, null, $music_favorite, $this, 'music');
      if ($res['statusCode'] = '1') {
        $resp = ['msg' => 'successfully!', 'code' => 200];
      } else {
        $resp = ['msg' => 'failed', 'code' => 201];
      }
    }
  } else {
    $resp = ['msg' => '请先登录', 'code' => 511];
  }
  return $this->response->withJson($resp);
});

$app->post('/home/gameFavOrLock', function ($request, $response) {
  $jwt = $request->getHeaderLine('Authorization');
  $bodyParams = $request->getParsedBody();
  $game_id = isset($bodyParams['game_id']) ? $bodyParams['game_id'] : null;
  $game_lock = isset($bodyParams['game_lock']) ? $bodyParams['game_lock'] : null;
  $game_favorite = isset($bodyParams['game_favorite']) ? $bodyParams['game_favorite'] : null;

  if ($jwt != '') {
    $resLogin = Utils::dealJwt($request, $jwt);
    if ($resLogin['statusCode'] == '4') {
      $res = Utils::doFavOrLock($resLogin, $game_id, $game_lock, $game_favorite, $this, 'game');
      if ($res['statusCode'] = '1') {
        $resp = ['msg' => 'successfully!', 'code' => 200];
      } else {
        $resp = ['msg' => 'failed', 'code' => 201];
      }
    }
  } else {
    $resp = ['msg' => '请先登录', 'code' => 511];
  }
  return $this->response->withJson($resp);
});

$app->get('/film/filmDetail', function ($request, $response) {
  $queryParams = $request->getQueryParams();
  $film_id = $queryParams['film_id'];
  $sql = 'SELECT * FROM films WHERE film_id = :film_id';
  $sth = $this->db->prepare($sql);
  $sqlArr = array(':film_id' => $film_id);
  $sth->execute($sqlArr);
  $codeOne = $sth->errorCode();
  $res = $sth->fetchAll();

  $sqlScore = "SELECT CONVERT( ( SELECT AVG(film_score) FROM filmscore WHERE film_id = :film_id ), DECIMAL(20, 1) ) AS film_score";
  $sthScore = $this->db->prepare($sqlScore);
  $sthScore->execute($sqlArr);
  $codeTwo = $sth->errorCode();
  $resScore = $sthScore->fetchAll();

  if ($codeOne == 00000 || $codeTwo == 00000) {
    $res[0]['film_score'] = $resScore[0]['film_score'];
    $resp = ['msg' => 'successfully!', 'code' => 200, 'data' => $res];
  } else {
    $resp = ['msg' => 'failed', 'code' => 201];
  }
  return $this->response->withJson($resp);
});

$app->get('/film/getFilmTalk', function ($request, $response) {
  $queryParams = $request->getQueryParams();
  $film_id = $queryParams['film_id'];
  $sql = 'SELECT * FROM filmtalk WHERE film_id = :film_id';
  $sth = $this->db->prepare($sql);
  $sqlArr = array(':film_id' => $film_id);
  $sth->execute($sqlArr);
  $codeOne = $sth->errorCode();
  $res = $sth->fetchAll();
  $resp = ['msg' => 'successfully!', 'code' => 200, 'data' => ['data' => $res]];
  return $this->response->withJson($resp);
});

$app->get('/home/homeDetailFilter', function ($request, $response) {
  $queryParams = $request->getQueryParams();
  $res = Utils::getItemSort($queryParams, $this);
  $resp = ['msg' => 'successfully!', 'code' => 200, 'data' => ['data' => $res]];
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
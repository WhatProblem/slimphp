<?php

/**
 * home:主页数据
 */
$app->get('/', function ($request, $response) {
  $sql = 'SELECT * FROM user';
  $sth = $this->db->prepare($sql);
  $sth->execute();
  $res = $sth->fetchAll();
  // $uri = $request->getUri();
  // $res = $uri->getBaseUrl();
  return $this->response->withJson($res);
  // $urls = '../assets/image/home/ai_ke.jpg';
  // $img = file_get_contents($urls, true);
  // header('Content-type:image/jpeg;text/html; charset=utf-8');
  // echo $img;
  // exit;
});

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
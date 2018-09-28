<?php
namespace Src\Common;

use \Firebase\JWT\JWT;

class Util
{
  // 服务器地址
	protected $serverAddr;

	public function __construct()
	{
		$this->serverAddr = $_SERVER['SERVER_ADDR'];
	}

	public function dealPic($data)
	{
    	// http://localhost/slimphp/assets/image/films/homePopFilm/1.jpg

		foreach ($data as $key => $value) {
			$posterDetail = $data[$key];
			if (isset($posterDetail['film_detail_poster'])) {
				$posterArr = explode('=', $posterDetail['film_detail_poster']);
				$data[$key]['film_detail_poster'] = 'http://' . $this->serverAddr . '/slimphp/assets/image/films/homePopFilm/' . $posterArr[2] . '.jpg';
			} else if (isset($posterDetail['music_detail_poster'])) {
				$posterArr = explode('=', $posterDetail['music_detail_poster']);
				$data[$key]['music_detail_poster'] = 'http://' . $this->serverAddr . '/slimphp/assets/image/musics/homePopMusic/' . $posterArr[2] . '.jpg';
			} else if (isset($posterDetail['game_detail_poster'])) {
				$posterArr = explode('=', $posterDetail['game_detail_poster']);
				$data[$key]['game_detail_poster'] = 'http://' . $this->serverAddr . '/slimphp/assets/image/games/homePopGame/' . $posterArr[2] . '.jpg';
			}
		}
		return $data;
	}

	/**
	 * 解析jwt
	 */
	static function dealJwt($req, $jwt)
	{
		try {
			JWT::$leeway = 60;//当前时间减去60，把时间留点余地
			$decode = JWT::decode($jwt, KEY, ['HS256']); //HS256方式，这里要和签发的时候对应
			$arr = (array)$decode;
			$resp = ['statusCode' => '4', 'msg' => 'successfully', 'decode' => $arr];
		} catch (\Firebase\JWT\SignatureInvalidException $e) { 
      //签名不正确
			$resp = ['statusCode' => '0', 'msg' => $e->getMessage()];
		} catch (\Firebase\JWT\BeforeValidException $e) { 
      // 签名在某个时间点之后才能用
			$resp = ['statusCode' => '1', 'msg' => $e->getMessage()];
		} catch (\Firebase\JWT\ExpiredException $e) {
      // token过期
			$resp = ['statusCode' => '2', 'msg' => $e->getMessage()];
		} catch (Exception $e) { 
      //其他错误
			$resp = ['statusCode' => '3', 'msg' => $e->getMessage()];
		}
		return $resp;
	}
	/**
	 * Note:处理收藏或加锁展示数据
	 * fav：收藏，lock:加锁
	 */
	static function showLockOrFav($self, $resLogin, $sqlInfo, $res, $favOrLock, $id)
	{
		$username = $resLogin['decode']['data']->username;
		$sqlFavOrLock = "SELECT * FROM $sqlInfo WHERE user_id = :user_id";
		$sthFav = $self->db->prepare($sqlFavOrLock);
		$sthFav->execute(array(':user_id' => $username));
		$resFav = $sthFav->fetchAll();

		foreach ($res as $key => $value) {
			foreach ($resFav as $favKey => $favValue) {
				if ($value[$id] == $favValue[$id]) {
					$res[$key][$favOrLock] = '1';
				}
			}
		}
		return $res;
	}
	/**
	 * description：更改数据
	 * Note:操作收藏加锁
	 * 所有项目的收藏加锁处理
	 */
	static function doFavOrLock($resLogin, $item_id, $item_lock, $item_favorite, $self, $flag)
	{
		$user_id = $resLogin['decode']['data']->username;
		if (isset($item_lock) && $item_lock == '0' && $flag == 'film') {
			$sql = 'DELETE FROM filmlock WHERE user_id = :user_id AND film_id = :film_id';
			$sqlArr = array(':user_id' => $user_id, ':film_id' => $item_id);
		} else if (isset($item_lock) && $item_lock == '1' && $flag == 'film') {
			$sql = 'INSERT INTO filmlock ( user_id, film_id ) VALUES ( :user_id, :film_id )';
			$sqlArr = array(':user_id' => $user_id, ':film_id' => $item_id);
		} else if (isset($item_favorite) && $item_favorite == '0' && $flag == 'film') {
			$sql = 'DELETE FROM filmfav WHERE user_id = :user_id AND film_id = :film_id';
			$sqlArr = array(':user_id' => $user_id, ':film_id' => $item_id);
		} else if (isset($item_favorite) && $item_favorite == '1' && $flag == 'film') {
			$sql = 'INSERT INTO filmfav ( user_id, film_id ) VALUES ( :user_id, :film_id )';
			$sqlArr = array(':user_id' => $user_id, ':film_id' => $item_id);
		} else if (isset($item_favorite) && $item_favorite == '0' && $flag == 'music') {
			$sql = 'DELETE FROM musicfav WHERE user_id = :user_id AND music_id = :music_id';
			$sqlArr = array(':user_id' => $user_id, ':music_id' => $item_id);
		} else if (isset($item_favorite) && $item_favorite == '1' && $flag == 'music') {
			$sql = 'INSERT INTO musicfav ( user_id, music_id ) VALUES ( :user_id, :music_id )';
			$sqlArr = array(':user_id' => $user_id, ':music_id' => $item_id);
		} else if (isset($item_lock) && $item_lock == '0' && $flag == 'game') {
			$sql = 'DELETE FROM gamelock WHERE user_id = :user_id AND game_id = :game_id';
			$sqlArr = array(':user_id' => $user_id, ':game_id' => $item_id);
		} else if (isset($item_lock) && $item_lock == '1' && $flag == 'game') {
			$sql = 'INSERT INTO gamelock ( user_id, game_id ) VALUES ( :user_id, :game_id )';
			$sqlArr = array(':user_id' => $user_id, ':game_id' => $item_id);
		} else if (isset($item_favorite) && $item_favorite == '0' && $flag == 'game') {
			$sql = 'DELETE FROM gamefav WHERE user_id = :user_id AND game_id = :game_id';
			$sqlArr = array(':user_id' => $user_id, ':game_id' => $item_id);
		} else if (isset($item_favorite) && $item_favorite == '1' && $flag == 'game') {
			$sql = 'INSERT INTO gamefav ( user_id, game_id ) VALUES ( :user_id, :game_id )';
			$sqlArr = array(':user_id' => $user_id, ':game_id' => $item_id);
		}
		$sth = $self->db->prepare($sql);
		$sth->execute($sqlArr);
		$code = $sth->errorCode();
		if ($code == 00000) {
			$resp['statusCode'] = '1';
		} else {
			$resp['statusCode'] = '0';
		}
		return $resp;
	}
	/**
	 * Note: film, music, game 分类及分页
	 */
	static function getItemSort($query, $self)
	{
		if (isset($query['film_score'])) {
// $sql = 'SELECT * FROM films WHERE film_score LIKE :film_score AND film_time LIKE :film_time AND film_type LIKE :film_type ORDER BY create_date DESC LIMIT :pages_index, :pages_total';
			$sql = "SELECT * FROM films WHERE film_score LIKE :film_score AND film_time LIKE :film_time AND film_type LIKE :film_type ORDER BY create_date';
			$query['film_type'] = $query['film_type'] == '0' ? '' : $query['film_type'];
			$query['film_time'] = $query['film_time'] == '0' ? '' : $query['film_time'];

			$sqlArr = array(
				':film_score' => '%' . $query['film_score'] . '%',
				':film_time' => '%' . $query['film_time'] . '%',
				':film_type' => '%' . $query['film_type'] . '%',
				':pages_index' => (int)$query['pages_index'],
				':pages_total' => (int)$query['pages_total']
			);
			// $sqlArr = array(
			// 	':film_score' => '%' . $query['film_score'] . '%',
			// 	':film_time' => '%' . $query['film_time'] . '%',
			// 	':film_type' => '%' . $query['film_type'] . '%'
			// );
		}
		$sth = $self->db->prepare($sql);
		$sth->execute($sqlArr);
		$code = $sth->errorCode();
		if ($code == 00000) {
			$res = $sth->fetchAll();
		}
		return $res;
	}
}
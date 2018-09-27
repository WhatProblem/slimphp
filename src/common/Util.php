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
}
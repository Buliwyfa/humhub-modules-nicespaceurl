<?php

namespace humhub\modules\nicespaceurl\components;

use yii\helpers\Url;
use yii\web\UrlRuleInterface;
use yii\base\Object;
use humhub\modules\space\models\Space;

class UrlRule extends Object implements UrlRuleInterface {
	public $defaultRoute = 'space/space';
	public function createUrl($manager, $route, $params) {
		if (isset ( $params ['sguid'] )) {
			if ($route == $this->defaultRoute) {
				$route = '';
			}
			
			$space = Space::find ()->where ( [ 
					'guid' => $params ['sguid'] 
			] )->one ();
			if (! $space)
				return false;
			
			$url = 's/' . $this->toAscii ( strtolower ( $space->name ) ) . '/' . $route;
			unset ( $params ['sguid'] );
			if (! empty ( $params ) && ($query = http_build_query ( $params )) !== '') {
				$url .= '?' . $query;
			}
			return $url;
		}
		return false;
	}
	public function parseRequest($manager, $request) {
		$pathInfo = $request->getPathInfo ();
		if (substr ( $pathInfo, 0, 2 ) == "s/") {
			$parts = explode ( '/', $pathInfo, 3 );
			if (isset ( $parts [1] )) {
				$space = null;
				
				// search nice url part for space
				$url_part = $this->toAscii ( strtolower ( $parts [1] ) );
				foreach ( Space::find ()->all () as $s ) {
					$tmp_name = $this->toAscii ( strtolower ( $s->name ) );
					if (strcmp ( $url_part, $tmp_name ) == 0) {
						$space = $s;
					}
				}
				
				// fallback for guid
				if ($space === null)
					$space = Space::find ()->where ( [ 
							'guid' => $parts [1] 
					] )->one ();
				
				if ($space !== null) {
					if (! isset ( $parts [2] ) || $parts [2] == "") {
						$parts [2] = $this->defaultRoute;
					}
					$params = $request->get ();
					$params ['sguid'] = $space->guid;
					return [ 
							$parts [2],
							$params 
					];
				}
			}
		}
		return false;
	}
	
	// from http://stackoverflow.com/a/4783820
	private function toAscii($str, $replace = [], $delimiter = '-') {
		if (! empty ( $replace )) {
			$str = str_replace ( ( array ) $replace, ' ', $str );
		}
		
		$clean = iconv ( 'UTF-8', 'ASCII//TRANSLIT', $str );
		$clean = preg_replace ( "/[^a-zA-Z0-9\/_|+ -]/", '', $clean );
		$clean = strtolower ( trim ( $clean, '-' ) );
		$clean = preg_replace ( "/[\/_|+ -]+/", $delimiter, $clean );
		
		return $clean;
	}
}

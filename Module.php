<?php

namespace humhub\modules\nicespaceurl;

use Yii;

class Module extends \humhub\components\Module {
	public static function examineUrlRules(\yii\base\Event $event) {
		$rules = Yii::$app->urlManager->rules;
		foreach ( $rules as $key => $rule ) {
			// removes humhub url generation for space
			if ($rule instanceof \humhub\modules\space\components\UrlRule)
				unset ( $rules [$key] );
		}
		Yii::$app->urlManager->rules = $rules;
	}
}

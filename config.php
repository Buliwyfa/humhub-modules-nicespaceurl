<?php
use humhub\components\Application;
use humhub\modules\startspace\controllers\StartspaceController;

return [ 
		'id' => 'nicespaceurl',
		'class' => 'humhub\modules\nicespaceurl\Module',
		'namespace' => 'humhub\modules\nicespaceurl',
		'urlManagerRules' => [ 
				[ 
						'class' => 'humhub\modules\nicespaceurl\components\UrlRule' 
				] 
		],
		'events' => [ 
				[ 
						'class' => Application::className (),
						'event' => Application::EVENT_BEFORE_REQUEST,
						'callback' => [ 
								'humhub\modules\nicespaceurl\Module',
								'examineUrlRules' 
						] 
				] 
		] 
];

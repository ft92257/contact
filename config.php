<?php

return [
		'DB_CONFIG' => 'mysqli://root:molbase1010@192.168.13.201:3306/contacts',
		'DB_MOLBASE' => 'mysqli://root:molbase1010@192.168.13.201:3306/molbase',
		
		'upload'       => [
            'image' => [
                'allowExts' => ['jpg', 'gif', 'png', 'jpeg'],
                'maxSize'   => 3145728,
                'savePath'  => 'images/',
            ],
            'video' => array(
                'allowExts' => array('avi', 'mp4'),
                'maxSize'   => 3145728,
                'savePath'  => 'video/',
            ),
        ],
		
        'QINIU_BUCKET' => 'contacts',
        'QINIU_KEY'    => 'St2X83Fk1zBnRcWv1F8WhJogUpi90eo7jc5qXaWe',
        'QINIU_SECRET' => 'GtM9AMbUX5BBrzhYBtkFoH2UzP8Zce8wM0mS3mIm',
        'QINIU_DOMAIN' => '7xqnn7.com1.z0.glb.clouddn.com',

        'JPUSH_KEY' => '6adef14ea7c85566faa72db3',
        'JPUSH_SECRET' => 'f78eea1b0d38f50230d9fe8a',
        'JPUSH_PRODUCTION' => false,
		
		'HX_PATH' => '/ft92257/renmai',
		'HX_CLIENT_ID' => 'YXA6vmw5cOBPEeWeGF8Rz5_Cow',
		'HX_CLIENT_SECRET' => 'YXA6riYaC3Hx389goqc-SxiiKGURbqM',
];

?>
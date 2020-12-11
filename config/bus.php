<?php

return [
    'user' => [ // 服务名
        'http' => [ // http配置
            'servers' => env('MICRO_GATEWAY') ? explode(',', env('MICRO_GATEWAY')) : [],
            'connectTimeout' => IS_CLI ? 2000 : 200,
        ]
    ],
    'order' => [ // 服务名
        'http' => [ // http配置
            'servers' => env('MICRO_GATEWAY') ? explode(',', env('MICRO_GATEWAY')) : [],
            'connectTimeout' => IS_CLI ? 2000 : 200,
        ]
    ],
    'sale' => [ // 服务名
        'http' => [ // http配置
            'servers' => env('SALE_GATEWAY') ? explode(',', env('SALE_GATEWAY')) : [],
            'connectTimeout' => IS_CLI ? 2000 : 200,
        ]
    ],
    'recommend' => [ // 服务名
        'http' => [ // http配置
            'servers' => env('MICRO_GATEWAY') ? explode(',', env('MICRO_GATEWAY')) : [],
            'connectTimeout' => IS_CLI ? 2000 : 200,
        ]
    ],
    'shop' => [ // 服务名
        'http' => [ // http配置
            'servers' => env('MICRO_GATEWAY') ? explode(',', env('MICRO_GATEWAY')) : [],
            'connectTimeout' => IS_CLI ? 2000 : 200,
        ]
    ]
];

<?php

return [

    'hidrologi' => [
        // 'tma' => [
          //     'prefix' => 'tinggi-muka-air',
          //     'name' => 'tma.',
          //     'config' => [
          //         'baseLabelUrl' => 'admin.hidrologi.tma',
          //         'viewPath' => 'admin.pages.dt_general.',
          //         'generalTitle' => 'Data Tinggi Muka Air',
          //         'filterTag' => 'data tinggi muka air',
          //     ],
        // ],
        'debit' => [
            'prefix' => 'debit',
            'name' => 'debit.',
            'config' => [
                'baseLabelUrl' => 'admin.hidrologi.debit',
                'viewPath' => 'admin.pages.dt_general.',
                'generalTitle' => 'Data Debit',
                'filterTag' => 'data debit',
                'filterCategories'  => 'Data hidrologi',
              ],
        ],
        'sedimen' => [
            'prefix' => 'sedimen',
            'name' => 'sedimen.',
            'config' => [
                'baseLabelUrl' => 'admin.hidrologi.sedimen',
                'viewPath' => 'admin.pages.dt_general.',
                'generalTitle' => 'Data Sedimen',
                'filterTag' => 'data sedimen',
                'filterCategories'  => 'Data hidrologi',
              ],
        ],
        'klimatologi' => [
            'prefix' => 'data-klimatologi',
            'name' => 'klimatologi.',
            'config' => [
                'baseLabelUrl' => 'admin.hidrologi.klimatologi',
                'viewPath' => 'admin.pages.dt_general.',
                'generalTitle' => 'Data Klimatologi',
                'filterTag' => 'data klimatologi',
                'filterCategories'  => 'Data hidrologi',
              ],
        ],
    ],

    'meteorologi' => [
        // 'curahhujan' => [
        //     'prefix' => 'curah-hujan',
        //     'name' => 'curahhujan.',
        //     'config' => [
        //         'baseLabelUrl' => 'admin.meteorologi.curahhujan',
        //         'viewPath' => 'admin.pages.dt_general.',
        //         'generalTitle' => 'Data Curah Hujan',
        //         'filterTag' => 'data curah hujan',
        //     ],
        // ],
    ],

    'geologi' => [
        'dmat' => [
            'prefix' => 'muka-air-tanah',
            'name' => 'dmat.',
            'config' => [
                'baseLabelUrl' => 'admin.geologi.dmat',
                'viewPath' => 'admin.pages.dt_general.',
                'generalTitle' => 'Data Muka Air Tanah',
                'filterTag' => 'data muka air tanah',
                'filterCategories'  => 'Data hidrogeologi',
              ],
        ],
        'minhid' => [
            'prefix' => 'minatan-hidrogeologi',
            'name' => 'minhid.',
            'config' => [
                'baseLabelUrl' => 'admin.geologi.minhid',
                'viewPath' => 'admin.pages.dt_general.',
                'generalTitle' => 'Data Minatan Hidrogeologi',
                'filterTag' => 'data minatan hidrogeologi',
                'filterCategories'  => 'Data hidrogeologi',
              ],
        ],
        'kualitasair' => [
            'prefix' => 'kualitas-air-tanah',
            'name' => 'kualitasair.',
            'config' => [
                'baseLabelUrl' => 'admin.geologi.kualitasair',
                'viewPath' => 'admin.pages.dt_general.',
                'generalTitle' => 'Data Kualitas Air Tanah',
                'filterTag' => 'data kualitas air tanah',
                'filterCategories'  => 'Data hidrogeologi',
              ],
        ],
        'cat' => [
            'prefix' => 'cekungan-air-tanah',
            'name' => 'cat.',
            'config' => [
                'baseLabelUrl' => 'admin.geologi.cat',
                'viewPath' => 'admin.pages.dt_general.',
                'generalTitle' => 'Cekungan Air Tanah',
                'filterTag' => 'cekungan air tanah',
                'filterCategories'  => 'Data hidrogeologi',
              ],
        ],
        'hidrogeo' => [
            'prefix' => 'hidrogeologi',
            'name' => 'hidrogeo.',
            'config' => [
                'baseLabelUrl' => 'admin.geologi.hidrogeo',
                'viewPath' => 'admin.pages.dt_general.',
                'generalTitle' => 'Data Hidrogeologi',
                'filterTag' => 'data hidrogeologi',
                'filterCategories'  => 'Data hidrogeologi',
              ],
        ],
    ],

];

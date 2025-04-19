<?php

return [

    'column_toggle' => [

        'heading' => '欄位',

    ],

    'columns' => [

        'actions' => [
            'label' => '操作|操作',
        ],

        'text' => [

            'actions' => [
                'collapse_list' => '顯示少 :count 項',
                'expand_list' => '顯示多 :count 項',
            ],

            'more_list_items' => '以及 :count 項更多',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => '選擇/取消選擇所有項目以進行批量操作。',
        ],

        'bulk_select_record' => [
            'label' => '選擇/取消選擇項目 :key 以進行批量操作。',
        ],

        'bulk_select_group' => [
            'label' => '選擇/取消選擇群組 :title 以進行批量操作。',
        ],

        'search' => [
            'label' => '搜尋',
            'placeholder' => '搜尋',
            'indicator' => '搜尋',
        ],

    ],

    'summary' => [

        'heading' => '摘要',

        'subheadings' => [
            'all' => '所有 :label',
            'group' => ':group 摘要',
            'page' => '此頁',
        ],

        'summarizers' => [

            'average' => [
                'label' => '平均',
            ],

            'count' => [
                'label' => '計數',
            ],

            'sum' => [
                'label' => '總和',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => '完成重新排序記錄',
        ],

        'enable_reordering' => [
            'label' => '重新排序記錄',
        ],

        'filter' => [
            'label' => '篩選',
        ],

        'group' => [
            'label' => '分組',
        ],

        'open_bulk_actions' => [
            'label' => '打開動作',
        ],

        'toggle_columns' => [
            'label' => '顯示／隱藏直列',
        ],

    ],

    'empty' => [

        'heading' => '沒有 :model',

        'description' => '創建一個 :model 以開始。',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => '應用篩選',
            ],

            'remove' => [
                'label' => '移除篩選',
            ],

            'remove_all' => [
                'label' => '移除所有篩選',
                'tooltip' => '移除所有篩選',
            ],

            'reset' => [
                'label' => '重設篩選',
            ],

        ],

        'heading' => '篩選條件',

        'indicator' => '活動篩選',

        'multi_select' => [
            'placeholder' => '全部',
        ],

        'select' => [
            'placeholder' => '全部',
        ],

        'trashed' => [

            'label' => '已刪除的資料',

            'only_trashed' => '僅顯示已刪除的資料',

            'with_trashed' => '包含已刪除的資料',

            'without_trashed' => '不含已刪除的資料',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => '分組依據',
                'placeholder' => '分組依據',
            ],

            'direction' => [

                'label' => '分組方向',

                'options' => [
                    'asc' => '升序',
                    'desc' => '降序',
                ],

            ],

        ],

    ],

    'reorder_indicator' => '拖曳以重新排序',

    'selection_indicator' => [

        'selected_count' => '已選擇 :count 個項目',

        'actions' => [

            'select_all' => [
                'label' => '選擇全部 :count 項',
            ],

            'deselect_all' => [
                'label' => '取消選擇全部',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => '排序依據',
            ],

            'direction' => [

                'label' => '排序方向',

                'options' => [
                    'asc' => '升序',
                    'desc' => '降序',
                ],

            ],

        ],

    ],

];

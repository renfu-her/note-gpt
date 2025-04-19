<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => '複製',
            ],

            'add' => [

                'label' => '加至 :label',

                'modal' => [

                    'heading' => '加至 :label',

                    'actions' => [

                        'add' => [
                            'label' => '新增',
                        ],

                    ],

                ],

            ],

            'add_between' => [

                'label' => '在區塊之間插入',

                'modal' => [

                    'heading' => '加至 :label',

                    'actions' => [

                        'add' => [
                            'label' => '新增',
                        ],

                    ],

                ],

            ],

            'delete' => [
                'label' => '刪除',
            ],

            'edit' => [

                'label' => '編輯',

                'modal' => [

                    'heading' => '編輯區塊',

                    'actions' => [

                        'save' => [
                            'label' => '儲存變更',
                        ],

                    ],

                ],

            ],

            'reorder' => [
                'label' => '移動',
            ],

            'move_down' => [
                'label' => '上移',
            ],

            'move_up' => [
                'label' => '下移',
            ],

            'collapse' => [
                'label' => '收起',
            ],

            'expand' => [
                'label' => '展開項目',
            ],

            'collapse_all' => [
                'label' => '收起全部',
            ],

            'expand_all' => [
                'label' => '展開全部',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => '取消選擇全部',
            ],

            'select_all' => [
                'label' => '選擇全部',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => '取消',
                ],

                'drag_crop' => [
                    'label' => '拖動模式「裁剪」',
                ],

                'drag_move' => [
                    'label' => '拖動模式「移動」',
                ],

                'flip_horizontal' => [
                    'label' => '水平翻轉圖片',
                ],

                'flip_vertical' => [
                    'label' => '垂直翻轉圖片',
                ],

                'move_down' => [
                    'label' => '向下移動圖片',
                ],

                'move_left' => [
                    'label' => '向左移動圖片',
                ],

                'move_right' => [
                    'label' => '向右移動圖片',
                ],

                'move_up' => [
                    'label' => '向上移動圖片',
                ],

                'reset' => [
                    'label' => '重置',
                ],

                'rotate_left' => [
                    'label' => '向左旋轉圖片',
                ],

                'rotate_right' => [
                    'label' => '向右旋轉圖片',
                ],

                'set_aspect_ratio' => [
                    'label' => '設定長寬比為 :ratio',
                ],

                'save' => [
                    'label' => '保存',
                ],

                'zoom_100' => [
                    'label' => '縮放圖片至 100%',
                ],

                'zoom_in' => [
                    'label' => '放大',
                ],

                'zoom_out' => [
                    'label' => '縮小',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => '高度',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => '旋轉',
                    'unit' => '度',
                ],

                'width' => [
                    'label' => '寬度',
                    'unit' => 'px',
                ],

                'x_position' => [
                    'label' => 'X 位置',
                    'unit' => 'px',
                ],

                'y_position' => [
                    'label' => 'Y 位置',
                    'unit' => 'px',
                ],

            ],

            'aspect_ratios' => [

                'label' => '長寬比',

                'no_fixed' => [
                    'label' => '自由',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => '不建議編輯 SVG 檔案，因為在縮放時可能會導致品質損失。\n 您確定要繼續嗎？',
                    'disabled' => '已禁用編輯 SVG 檔案，因為在縮放時可能會導致品質損失。',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => '新增橫列',
            ],

            'delete' => [
                'label' => '刪除橫列',
            ],

            'reorder' => [
                'label' => '重新排序橫列',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => '索引鍵',
            ],

            'value' => [
                'label' => '值',
            ],

        ],

    ],

    'markdown_editor' => [

        'toolbar_buttons' => [
            'attach_files' => '附加檔案',
            'blockquote' => '引用區塊',
            'bold' => '粗體',
            'bullet_list' => '無序清單',
            'code_block' => '程式碼區塊',
            'heading' => '標題',
            'italic' => '斜體',
            'link' => '連結',
            'ordered_list' => '有序清單',
            'redo' => '取消復原',
            'strike' => '刪除線',
            'table' => '表格',
            'undo' => '復原',
        ],

    ],

    'radio' => [

        'boolean' => [
            'true' => '是',
            'false' => '否',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => '加至 :label',
            ],

            'add_between' => [
                'label' => '在項目之間插入',
            ],

            'delete' => [
                'label' => '刪除',
            ],

            'clone' => [
                'label' => '複製',
            ],

            'reorder' => [
                'label' => '移動',
            ],

            'move_down' => [
                'label' => '下移',
            ],

            'move_up' => [
                'label' => '上移',
            ],

            'collapse' => [
                'label' => '收起',
            ],

            'expand' => [
                'label' => '展開',
            ],

            'collapse_all' => [
                'label' => '收起全部',
            ],

            'expand_all' => [
                'label' => '展開全部',
            ],

        ],

    ],

    'rich_editor' => [

        'dialogs' => [

            'link' => [

                'actions' => [
                    'link' => '連結',
                    'unlink' => '取消連結',
                ],

                'label' => 'URL',

                'placeholder' => '輸入 URL',

            ],

        ],

        'toolbar_buttons' => [
            'attach_files' => '附加檔案',
            'blockquote' => '引用區塊',
            'bold' => '粗體',
            'bullet_list' => '無序清單',
            'code_block' => '程式碼區塊',
            'h1' => '大標題',
            'h2' => '小標題',
            'h3' => '附標題',
            'italic' => '斜體',
            'link' => '連結',
            'ordered_list' => '有序清單',
            'redo' => '取消復原',
            'strike' => '刪除線',
            'underline' => '底線',
            'undo' => '復原',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'label' => '建立',

                'modal' => [

                    'heading' => '建立',

                    'actions' => [

                        'create' => [
                            'label' => '建立',
                        ],

                        'create_another' => [
                            'label' => '建立並再建立一個',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'label' => '編輯',

                'modal' => [

                    'heading' => '編輯',

                    'actions' => [

                        'save' => [
                            'label' => '儲存',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => '是',
            'false' => '否',
        ],

        'loading_message' => '載入中...',

        'max_items_message' => '只能選擇 :count 個項目。',

        'no_search_results_message' => '未有符合該選項的搜尋結果。',

        'placeholder' => '選擇選項',

        'searching_message' => '搜尋中...',

        'search_prompt' => '輸入以搜尋...',

    ],

    'tags_input' => [
        'placeholder' => '新增標籤',
    ],

    'text_input' => [

        'actions' => [

            'hide_password' => [
                'label' => '隱藏密碼',
            ],

            'show_password' => [
                'label' => '顯示密碼',
            ],

        ],

    ],

    'toggle_buttons' => [

        'boolean' => [
            'true' => '是',
            'false' => '否',
        ],

    ],

    'wizard' => [

        'actions' => [

            'previous_step' => [
                'label' => '返回',
            ],

            'next_step' => [
                'label' => '繼續',
            ],

        ],

    ],

];

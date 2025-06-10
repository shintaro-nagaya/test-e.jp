<?php
namespace App\Utils;

class CKEditorConfigUtil {
    static public array $defaultConfig = [
        'uiColor' => "#cccccc",
        'language' => 'ja',
        'toolbar' => [
            [
                "name" => "document",
                "items" => [
                    "Source"
                ]
            ],
            [
                "name" => "clipboard",
                "items" => [
                    "Undo", "Redo"
                ],
            ],
            [
                "name" => "basicstyles",
                "items" => [
                    "Bold", "Italic", "Underline", "Strike", "Subscript", "Superscript","-", "RemoveFormat"
                ],
            ],
            [
                "name" => "links",
                "items" => [
                    "Link", "Iframe"
                ]
            ],
            [
                "name" => "colors",
                "items" => [
                    "TextColor", "BGColor"
                ]
            ]
        ],
        'width' => "900px",
        'height' => "320px",
        'required' => true,
        'extraAllowedContent' => 'span(allow_class_name)'
    ];
}

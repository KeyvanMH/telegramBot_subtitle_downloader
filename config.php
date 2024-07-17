<?php

define("TOKEN", "****");
define('URL', 'https://api.telegram.org/bot' . TOKEN . '/');
define("SERVERNAME", "127.0.0.1");
define("ADMIN_TOKEN", "46079a0373d4553ded472e9829b68ac86a30468e2f4898bc57902408173b74cd46079a0373d4553ded472e9829b68ac86a30468e2f4898bc57902408173b74cd");

define("USERNAME", "****");
define("DBNAME", "subtitle");
define("PASSWORD", "******");
//message
define("RETURN_HOME", "برای برگشت به خانه روی کلمه /home کلیک کنید.");

define("GREET1", "خوش اومدی ");
define("GREET2", " عزیز!\n  برای دانلود زیرنویس رو دکمه #دانلود_زیرنویس کلیک کن.\n\n  میدونستی که میتونی تو ربات ما ثبت نام کنی و فیلم مورد نظرت که هنوز زیرنویسش هنوز نیومده رو به محض اومدن داشته باشی؟\n  برای ادامه  رو دکمه #ثبت_نام  کلیک کن.");

define("HOME_SIGNED", "چه کاری میتونم برات انجام بدم؟\nبرای مشاهده،اضافه یا حذف کردن زیرنویس های درخواستی ، از دکمه های زیر استفاده کنید. ");
define("HOME_NOT_SIGNED", "چه کاری میتونم برات انجام بدم؟\n میدونستی برای فیلم های که تازه اومدن و زیرنویس ندارن درخواست بدی تا هر وقت موجود شدن برات فرستاده بشن؟\nبرای درخواست زیر نویس روی دکمه #ثبت_نام کلیک کن.");

define("SIGNUP_FALSE", "شما قبلا ثبت نام کرده اید");
define("SIGNUP_TRUE", "ثبت نام شما با موفقیت انجام شد");
define("SEE_MESSAGE", "رزرو های شما بصورت زیر است:");
define("ADD_MESSAGE", "اسم فیلمی که می خواهید به لیست خود اضافه کنید را بفرستید.");
define("DELETE_MESSAGE", "اسم فیلمی که می خواهید حذف کنید را بفرستید.");
define("DOWNLOAD_MESSAGE", "اسم فیلمی که می خواهید دانلود کنید را بفرستید.");
define("NOT_FOUND", "موردی یافت نشد");
define("ERROR", ".لطقا دوباره امتحان کنید  \n  برای بازگشت به صفحه اصلی روی  /home کلیک کنید");
define("SET_ADMIN", "تبدیل به ادمین با موفقیت انجام شد.");
define("ADD_REQUEST_OK", "درخواست زیرنویس با موفقیت اضافه شد.\nبرای برگشت به خانه روی /home کلیک کنید.");
define("ADD_REQUEST_FAIL", "تعداد درخواست شما به اخر رسیده است.\n شما میتوانید برای اضافه کردن زیرنویس درخواستی خود، ابتدا از درخواست های خود زیرنویسی را حذف و سپس اقدام به درخواست زیرنویس کنید.\n برای برگشت به خانه روی /home کلیک کنید. ");
define("DELETE_OK", "زیرنویس درخواستی شما با موفقیت از لیست شما حذف شد.\n برای برگشت به خانه روی /home کلیک کنید.");

define("DOWNLOAD_REQUEST", "اسم فیلمی که می خواهید دانلود کنید را بفرستید");
define("ADD_REQUEST", "اسم فیلمی که می خواهید به لیست خود اضافه کنید را بفرستید");
define("DELETE_REQUEST", "اسم فیلمی که می خواهید حذف کنید را بفرستید");
//json message
define("GREET_JSON", [
    "inline_keyboard" => [
        [
            [
                "text" => "ثبت نام",
                "callback_data" => "signUp"
            ],
            [
                "text" => "دانلود زیرنویس",
                "callback_data" => "download"
            ]
        ]
    ]
]);
define("HOME_SIGNED_JSON", [
    "inline_keyboard" => [
        [
            [
                "text" => " دانلود زیرنویس",
                "callback_data" => "download"
            ]
        ], [
            [
                "text" => "مشاهده زیرنویس هایتان ",
                "callback_data" => "seeMessage"
            ], [
                "text" => "اضافه کردن زیرنویس",
                "callback_data" => "addMessage"
            ], [
                "text" => " حذف زیرنویس هایتان",
                "callback_data" => "deleteMessage"
            ]
        ]
    ]
]);
define("HOME_NOT_SIGNED_JSON",
    [
        "inline_keyboard" => [
            [
                [
                    "text" => "ثبت نام",
                    "callback_data" => "signUp"
                ],
                [
                    "text" => "دانلود زیرنویس",
                    "callback_data" => "download"
                ]
            ]
        ]
    ]);
define("FORCE_REPLY", [
    "force_reply" => true,
    "input_field_placeholder" => "نام فیلم مورد نظر..."
]);
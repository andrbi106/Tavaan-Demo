<?php
declare(strict_types=1);
namespace Tavaan\Core\Application;

/** حالت اجرای اپلیکیشن. */
enum RuntimeMode: string
{
    case Production = 'production';
    case Development = 'development';
    case Testing = 'testing';
    case Console = 'console';
}

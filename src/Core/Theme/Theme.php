<?php

namespace SLTest\Core\Theme;


use SLTest\Core\Exception\InvalidArgumentException;
use SLTest\Core\Kernel\Kernel;

class Theme
{
    /**
     * Рендерит указанный шаблон.
     *
     * @param string $template имя шаблона.
     * @param array $vars парамтеры шаблона.
     *
     * @return string отрендеренный контент шаблона.
     */
    public static function render($template, array $vars)
    {
        $theme = Kernel::config()['theme'] ?? 'default';
        $path = TEMPLATE_DIR . '/' . $theme . '/' . $template . '.html.php';
        if (!file_exists($path)) {
            throw new InvalidArgumentException(sprintf('Шаблон "%s" не найден в теме "%s".', $template, $theme));
        }

        ob_start();
        extract($vars);
        include $path;
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }
}
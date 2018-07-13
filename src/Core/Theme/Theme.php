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
        $_file_path = TEMPLATE_DIR . '/' . $theme . '/' . $template . '.html.php';
        if (!file_exists($_file_path)) {
            throw new InvalidArgumentException(sprintf('Шаблон "%s" не найден в теме "%s".', $template, $theme));
        }

        ob_start();
        extract($vars, EXTR_PREFIX_SAME, 'tpl_');
        include $_file_path;
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }
}
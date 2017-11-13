<?php
namespace Controller;

class View {

    /**
     * @param $template
     * @param array $data
     */
    public function render($template, $data = [])
    {
        $defaultTemplatePath = __DIR__.'/../templates/' . $template .'.phtml';

        if ($data) {
            extract($data);
        }

        if(!file_exists($defaultTemplatePath)) {
            die('template ' . $template . ' not found');
        }

        include  __DIR__.'/../templates/base.phtml';
    }
}
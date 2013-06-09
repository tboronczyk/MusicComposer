<?php
namespace Zaemis;

class Template
{
    protected $vars;
    protected $templateDir;

    public function __construct($templateDir) {
        $this->templateDir = $templateDir;
        $this->reset();
    }

    public function __set($var, $value) {
        $this->vars[$var] = $value;
    }

    public function __get($var) {
        return $this->vars[$var];
    }

    public function __isset($var) {
        return isset($this->vars[$var]);
    }

    public function reset() {
        $this->vars = array();
    }

    public function set($vars) {
        foreach ($vars as $k => $v) {
            $this->{$k} = $v;
        }
    }

    public function fetch($template, $output = true, $preserve = false) {
        ob_start();
        require $this->templateDir . $template;
        $content = ob_get_clean();

        if (!$preserve) {
            $this->reset();
        }

        if ($output) {
            echo $content;
        }
        else {
            return $content;
        }
    }
}

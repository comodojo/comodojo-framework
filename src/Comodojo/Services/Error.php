<?php namespace Comodojo\Services;

use \Comodojo\Qotd;
use \Comodojo\Dispatcher\Service\AbstractService;

class Authentication extends AbstractService {

    public function get() {

        $qotd = new \Comodojo\Qotd();

        $template = $this->configuration()->get('html-error-template');

        $html = file_get_contents($template);

        $needles = array(
            "{{ERROR_HEADER}}",
            "{{ERROR_CODE}}",
            "{{ERROR_MESSAGE}}",
            "{{ERROR_QUOTE}}"
        );

        $replacements = array(
            $this->extra()->get("error-header"),
            $this->extra()->get("error-code"),
            $this->extra()->get("error-message"),
            $qotd->getQuote()
        );

        return str_replace($needles, $replacements, $html);

    }

}

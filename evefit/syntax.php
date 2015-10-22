<?php
/**
 * EVE Online syntax plugin for EFT-style fits.
 * 
 * @license    CC-BY 4.0
 * @author     William Furr <william.furr@gmail.com>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_evefit extends DokuWiki_Syntax_Plugin {

    public function getType(){ return 'protected'; }
    public function getSort(){ return 158; }
    public function connectTo($mode) { $this->Lexer->addSpecialPattern('<[Ff]it>.*?</[Ff]it>',$mode,'plugin_evefit'); }

    /**
     * Handle the match
     */
    public function handle($match, $state, $pos, Doku_Handler $handler){
        return $match;
    }

    /**
     * Create output
     */
    public function render($mode, Doku_Renderer $renderer, $data) {
        // $data is what the function handle() return'ed.
        if($mode == 'xhtml'){
            preg_match('/(?:<[Ff]it>)([[:print:]\n]*?)(?:<\/[Ff]it>)/', $data, $matches);
            $fit = $matches[1];
            // TODO: POST fit to o.smium.org/api/convert/eft/dna input=$fit
            $renderer->doc .= "<p>EFT block replaced.</p><pre>";
            $renderer->doc .= $renderer->_xmlEntities($fit); 
            $renderer->doc .= "</pre>";
            return true;
        }
        return false;
    }
}

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
    public function getPType(){ return 'block'; }
    public function getSort(){ return 158; }
    public function connectTo($mode) { $this->Lexer->addSpecialPattern('\[[Ff]it\].*?\[/[Ff]it\]',$mode,'plugin_evefit'); }

    /**
     * Handle the match
     */
    public function handle($match, $state, $pos, Doku_Handler $handler) {
        preg_match('/(?:\[[Ff]it\]\n*)([[:print:]\n]*?)(?:\[\/[Ff]it\])/', $match, $matches);
        $fit = $matches[1];
        preg_match('/(\[[[:print:]]+\])/', $fit, $matches);
        $title = $matches[1];
        return array($fit, $dna, $stats, $title);
    }

    /**
     * Create output
     */
    public function render($mode, Doku_Renderer $renderer, $data) {
        // $data is what the function handle() return'ed.
        if($mode == 'xhtml') {
            list($fit, $dna, $stats, $title) = $data;
            $id = rand();
            $fitBody = nl2br($renderer->_xmlEntities($fit));
            $renderer->doc .= <<<EVEFIT
<div class="evefit-block">
  <div class="evefit-summary">
    <span class="evefit-expand" data-evefit-id="$id"></span>
    $title
    <span class="evefit-copy"
          data-clipboard-target="#evefit-$id">Copy to clipboard</span>
  </div>
  <div class="evefit-body" id="evefit-$id" style="display: none;">
$fitBody
  </div>
</div>
EVEFIT;

            return true;
        }
        return false;
    }
}
?>

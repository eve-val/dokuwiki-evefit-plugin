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
    public function connectTo($mode) { $this->Lexer->addSpecialPattern('\[[Ff]it\].*?\[/[Ff]it\]',$mode,'plugin_evefit'); }

    /**
     * Handle the match
     */
    public function handle($match, $state, $pos, Doku_Handler $handler) {
        preg_match('/(?:\[[Ff]it\]\n*)([[:print:]\n]*?)(?:\[\/[Ff]it\])/', $match, $matches);
        $fit = $matches[1];
        
        $curl_handle = curl_init("https://o.smium.org/api/convert/eft/dna");
        curl_setopt($curl_handle, CURLOPT_POST, True);
        curl_setopt($curl_handle, CURLOPT_POSTFIELDS, 'input='.urlencode($fit));
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, True);
        curl_setopt($curl_handle, CURLOPT_ENCODING, 'gzip');
        curl_setopt($curl_handle, CURLOPT_USERAGENT, 'DokuWikiEvefitPlugin/0.0.1 (+william.furr@gmail.com)');
        
        $dna = curl_exec($curl_handle);
        curl_close($curl_handle);
        
        $curl_handle = curl_init("https://o.smium.org/api/json/loadout/dna/attributes/loc:ship,a:ehpAndResonances,a:damage,a:priceEstimateTotal?input=".urlencode($dna));
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, True);
        curl_setopt($curl_handle, CURLOPT_ENCODING, 'gzip');
        curl_setopt($curl_handle, CURLOPT_USERAGENT, 'DokuWikiEvefitPlugin/0.0.1 (+william.furr@gmail.com)');
        
        $stats = curl_exec($curl_handle);
        curl_close($curl_handle);
        
        // TODO: POST fit to o.smium.org/api/convert/eft/dna input=$fit
        return array($fit, $dna, $stats);
    }

    /**
     * Create output
     */
    public function render($mode, Doku_Renderer $renderer, $data) {
        // $data is what the function handle() return'ed.
        if($mode == 'xhtml') {
            list($fit, $dna, $stats) = $data;
            $stats = json_decode($stats, True);
            
            $renderer->doc .= "<p><a href=\"https://o.smium.org/loadout/dna/";
            $renderer->doc .= $dna."\">Osmium</a> - ";
            $renderer->doc .= $stats['ship']['damage']['total']['dps']." DPS - ";
            $renderer->doc .= $stats['ship']['ehpAndResonances']['ehp']['avg']." EHP - ";
            $renderer->doc .= ($stats['ship']['priceEstimateTotal']['ship'] + $stats['ship']['priceEstimateTotal']['fitting'])." ISK";
            $renderer->doc .= "</p><pre>";
            $renderer->doc .= $renderer->_xmlEntities($fit); 
            $renderer->doc .= "</pre>";
            return true;
        }
        return false;
    }
}

?>
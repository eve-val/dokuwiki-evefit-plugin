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
        $dna = $this->_getDNAString($fit);
        $stats = $this->_getFitStats($dna);
        return array($fit, $dna, $stats);
    }

    /**
     * Create output
     */
    public function render($mode, Doku_Renderer $renderer, $data) {
        // $data is what the function handle() return'ed.
        if($mode == 'xhtml') {
            list($fit, $dna, $stats) = $data;
            $id = rand();
            $dps = $this->_abbreviateNumber($stats['damage']['total']['dps']);
            $ehp = $this->_abbreviateNumber(
                $stats['ehpAndResonances']['ehp']['avg']);
            $osmiumUrl = $this->external_link(
                "https://o.smium.org/loadout/dna/".$dna,
                "Osmium");
            $price = $this->_abbreviateNumber(
                        $stats['priceEstimateTotal']['ship'] +
                        $stats['priceEstimateTotal']['fitting']);
            $fitTitle = "TODO: extract title";
            $fitBody = $renderer->_xmlEntities($fit);
            
            $renderer->doc .= <<<EVEFIT
<div class="evefit-block">
  <div class="evefit-summary">
    <span class="evefit-button" 
          onclick="jQuery('#evefit-$id').toggle();"></span>
    $fitTitle - $dps DPS - $ehp EHP - $price ISK - $osmiumUrl
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
    
    // Given a fit in EFT format, queries Osmium for the DNA equivalent.
    private function _getDNAString($fit) {
        $curl_handle = $this->_initCurl("https://o.smium.org/api/convert/eft/dna");
        curl_setopt($curl_handle, CURLOPT_POST, True);
        curl_setopt($curl_handle, CURLOPT_POSTFIELDS, 'input='.urlencode($fit));
        $dna = curl_exec($curl_handle);
        curl_close($curl_handle);
        return $dna;
    }
    
    // Given a fit in DNA format, queries Osmium for fit stats.
    private function _getFitStats($dna) {
        $requestedStats = "a:ehpAndResonances,a:damage,a:priceEstimateTotal";
        $curl_handle = $this->_initCurl("https://o.smium.org/api/json/loadout/dna/attributes/loc:ship,".$requestedStats."?input=".urlencode($dna));
        $stats = curl_exec($curl_handle);
        curl_close($curl_handle);
        return json_decode($stats, True)['ship'];
    }
    
    // Makes a cURL handle with the right user agent and accept-encoding.
    private function _initCurl($url) {
        $curl_handle = curl_init($url);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, True);
        curl_setopt($curl_handle, CURLOPT_ENCODING, 'gzip');
        curl_setopt($curl_handle, CURLOPT_USERAGENT, 'DokuWikiEvefitPlugin/0.0.1 (+william.furr@gmail.com)');
        return $curl_handle;
    }
    
    // Given a number, returns the closest appropriate abbreviation.
    private function _abbreviateNumber($num) {      
          if ($num >= 1000000000) {
              $num = $num / 1000000000;
              $suffix = 'B';
          } else if ($num >= 1000000) {
              $num = $num / 1000000;
              $suffix = 'M';
          } else if ($num >= 1000) {
              $num = $num / 1000;
              $suffix = 'K';
          }
          if ($num > 99) {
              $ndecimals = 0;
          } else if ($num > 9) {
              $ndecimals = 1;
          } else {
              $ndecimals = 2;
          }
          return sprintf("%.".$ndecimals."f".$suffix, $num);
    }
}

?>
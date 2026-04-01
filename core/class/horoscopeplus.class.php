<?php
/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

require_once __DIR__ . '/../../../../core/php/core.inc.php';

class horoscopeplus extends eqLogic {

  public function toHtml($_version = 'dashboard') {
    if ($_version !== 'dashboard') {
      return parent::toHtml($_version);
    }

    try {
      $type   = strtolower($this->getConfiguration('type', 'chinois'));
      $themes = self::getThemesByType($type);

      // Logo et date
      $cmdLogo   = $this->getCmd('info', 'logo');
      $cmdDate   = $this->getCmd('info', 'date');
      $logoCache = is_object($cmdLogo) ? $cmdLogo->getCache('value') : null;
      $logoUrl   = (is_array($logoCache) && isset($logoCache['value'])) ? $logoCache['value'] : (is_string($logoCache) ? $logoCache : '');
      $dateCache = is_object($cmdDate) ? $cmdDate->getCache('value') : null;
      $date      = (is_array($dateCache) && isset($dateCache['value'])) ? $dateCache['value'] : (is_string($dateCache) ? $dateCache : '');

      // Filtre : hors Date/Logo + respect isVisible + ordre Jeedom (drag & drop)
      $displayThemes = array();
      $allCmds = $this->getCmd('info');
      // Trier par ordre Jeedom
      usort($allCmds, function($a, $b) {
        return intval($a->getOrder()) - intval($b->getOrder());
      });
      foreach ($allCmds as $cmd) {
        $logicalId = $cmd->getLogicalId();
        if (in_array($logicalId, array('date', 'logo'), true)) continue;
        if (intval($cmd->getIsVisible()) === 0) continue;
        $displayThemes[] = $cmd->getName();
      }

      $rowCount = count($displayThemes);

      // ID de la commande refresh pour le bouton de la tuile
      $refreshCmd   = $this->getCmd('action', 'refresh');
      $refreshCmdId = is_object($refreshCmd) ? $refreshCmd->getId() : 0;

      // Paramètres de style (configuration équipement)
      $col1_width  = $this->getConfiguration('col1_width',  '110px');
      $col1_bg     = $this->getConfiguration('col1_bg',     '');
      $col2_color  = $this->getConfiguration('col2_color',  '#ff0000');
      $col2_size   = $this->getConfiguration('col2_size',   '10px');
      $col2_align  = $this->getConfiguration('col2_align',  'left');
      $col3_color  = $this->getConfiguration('col3_color',  '#000000');
      $col3_size   = $this->getConfiguration('col3_size',   '10px');
      $col3_align  = $this->getConfiguration('col3_align',  'left');
      $col23_bg    = $this->getConfiguration('col23_bg',    '#cfcfcf');

      // Construction du tableau
      $table = '';
      if ($rowCount > 0) {
        $table .= '<table style="width:100%;border-collapse:collapse;"><tbody>';
        $first  = true;
        foreach ($displayThemes as $theme) {
          $logicalId = horoscopeplus::logicalIdFromTheme($theme);
          $cmd       = $this->getCmd('info', $logicalId);
          $valCache  = is_object($cmd) ? $cmd->getCache('value') : null;
          $value     = (is_array($valCache) && isset($valCache['value'])) ? $valCache['value'] : (is_string($valCache) ? $valCache : '');

          $table .= '<tr>';
          if ($first) {
            $col1Style = 'text-align:center;vertical-align:middle;padding:6px;width:' . htmlspecialchars($col1_width) . ';border:1px solid #ddd;';
            if ($col1_bg !== '') $col1Style .= 'background-color:' . htmlspecialchars($col1_bg) . ' !important;';
            $table .= '<td rowspan="' . $rowCount . '" style="' . $col1Style . '">';
            if ($logoUrl) {
              $table .= '<img src="' . htmlspecialchars($logoUrl) . '" style="width:' . htmlspecialchars($col1_width) . ';height:' . htmlspecialchars($col1_width) . ';display:block;margin:0 auto 4px auto;">';
            }
            $table .= '<span style="font-size:0.75em;color:#888;display:block;">' . htmlspecialchars($date) . '</span>';
            $table .= '<a class="bt_refreshHoroscopeWidget cursor" data-eqlogic-id="' . $this->getId() . '" data-refresh-cmd-id="' . $refreshCmdId . '" title="Rafraîchir" style="font-size:0.8em;color:#aaa;display:block;margin-top:4px;"><i class="fas fa-sync-alt"></i></a>';
            $table .= '<span style="font-size:8px;color:#bbb;display:block;margin-top:4px;word-break:break-all;">mon-horoscope-du-jour.com</span>';
            $table .= '</td>';
            $first = false;
          }
          $col2Style = 'font-weight:bold;padding:4px 8px;white-space:nowrap;vertical-align:top;border:1px solid #ddd;';
          $col2Style .= 'color:' . htmlspecialchars($col2_color) . ';';
          $col2Style .= 'font-size:' . htmlspecialchars($col2_size) . ' !important;';
          $col2Style .= 'text-align:' . htmlspecialchars($col2_align) . ';';
          if ($col23_bg !== '') $col2Style .= 'background-color:' . htmlspecialchars($col23_bg) . ' !important;';
          $table .= '<td style="' . $col2Style . '">';
          $table .= htmlspecialchars($theme) . '</td>';

          $col3Style = 'padding:4px 8px;vertical-align:top;border:1px solid #ddd;';
          $col3Style .= 'font-size:' . htmlspecialchars($col3_size) . ' !important;';
          $col3Style .= 'text-align:' . htmlspecialchars($col3_align) . ';';
          if ($col3_color !== '') $col3Style .= 'color:' . htmlspecialchars($col3_color) . ';';
          if ($col23_bg !== '') $col3Style .= 'background-color:' . htmlspecialchars($col23_bg) . ' !important;';
          $table .= '<td style="' . $col3Style . '">';
          $table .= htmlspecialchars($value) . '</td>';
          $table .= '</tr>';
        }
        $table .= '</tbody></table>';
      }

      // Utiliser parent::toHtml() pour le wrapper Jeedom natif (titre, design, scripts)
      $html = parent::toHtml($_version);
      // Remplacer tout le contenu de div.cmds par notre tableau
      $needle = '<div class="cmds ';
      $pos = strpos($html, $needle);
      if ($pos !== false) {
        $posOpen = strpos($html, '>', $pos) + 1;
        // Trouver la fermeture de div.cmds (on cherche </div> au bon niveau)
        $depth = 1;
        $i = $posOpen;
        while ($i < strlen($html) && $depth > 0) {
          if (substr($html, $i, 5) === '<div ') { $depth++; $i += 5; }
          elseif (substr($html, $i, 4) === '<div') { $depth++; $i += 4; }
          elseif (substr($html, $i, 6) === '</div>') { $depth--; if ($depth > 0) $i += 6; }
          else { $i++; }
        }
        $html = substr($html, 0, $posOpen) . $table . substr($html, $i);
      }
      // Ajouter le bouton refresh via JS dans la tuile
      $script = '<script>'
        . 'if(typeof horoscopeplusRefreshWidget === "undefined"){'
        . 'window.horoscopeplusRefreshWidget = true;'
        . '$(document).on("click",".bt_refreshHoroscopeWidget",function(){'
        . 'var icon=$(this).find("i");'
        . 'var cmdId=$(this).data("refresh-cmd-id");'
        . 'icon.addClass("fa-spin");'
        . 'jeedom.cmd.execute({'
        . 'id:cmdId,'
        . 'success:function(){icon.removeClass("fa-spin");},'
        . 'error:function(){icon.removeClass("fa-spin");}'
        . '});'
        . '});}'
        . '</script>';
      return $html . $script;

    } catch (Throwable $e) {
      log::add('horoscopeplus', 'error', 'toHtml: ' . $e->getMessage());
      return parent::toHtml($_version);
    }
  }


  public static function getThemesByType(string $type): array {
    $type = strtolower(trim($type));

    if ($type === 'chinois') {
      return ['Amour', 'Argent', 'Bien-être', 'Loisirs', 'Relation', 'Date', 'Logo'];
    }

    if ($type === 'occidental') {
      return ['Humeur', 'Amour', 'Argent', 'Travail', 'Loisirs', '1er Décan', '2ème Décan', '3ème Décan', 'Date', 'Logo'];
    }

    return [];
  }

  public function postSave() {
    // Initialiser les couleurs par défaut si pas encore définies (nouvel équipement)
    $defaults = [
      'col1_width' => '110px',
      'col2_color' => '#ff0000',
      'col2_size'  => '10px',
      'col3_color' => '#000000',
      'col3_size'  => '10px',
      'col23_bg'   => '#cfcfcf',
    ];
    $changed = false;
    foreach ($defaults as $key => $val) {
      if ($this->getConfiguration($key, '') === '') {
        $this->setConfiguration($key, $val);
        $changed = true;
      }
    }
    if ($changed) {
      $this->save(true);
    }

    $type  = strtolower($this->getConfiguration('type', 'chinois'));
    $signe = strtolower($this->getConfiguration('signe', ''));

    // 1) Calcule les logicalIds attendus pour le type actuel
    $themes        = self::getThemesByType($type);
    $expectedIds   = array_map([self::class, 'logicalIdFromTheme'], $themes);
    $expectedIds[] = 'refresh';

    // Supprime les commandes info obsolètes (ex: changement chinois -> occidental)
    foreach ($this->getCmd('info') as $cmd) {
      if (!in_array($cmd->getLogicalId(), $expectedIds, true)) {
        $cmd->remove();
      }
    }

    // 2) Crée/assure les commandes info du type actuel
    foreach ($themes as $theme) {
      $logicalId = self::logicalIdFromTheme($theme);

      $cmd = $this->getCmd('info', $logicalId);
      if (!is_object($cmd)) {
        $cmd = new horoscopeplusCmd();
        $cmd->setEqLogic_id($this->getId());
        $cmd->setType('info');
        $cmd->setSubType('string');
        $cmd->setLogicalId($logicalId);
      }
      $cmd->setName($theme);
      // Appliquer le widget logo_horoscope sur la commande Logo
      if ($logicalId === 'logo') {
        $cmd->setTemplate('dashboard', 'logo_horoscope');
        $cmd->setTemplate('mobile', 'logo_horoscope');
      }
      $cmd->save();
    }

    // 3) Commande action "Rafraîchir"
    $refresh = $this->getCmd('action', 'refresh');
    if (!is_object($refresh)) {
      $refresh = new horoscopeplusCmd();
      $refresh->setEqLogic_id($this->getId());
      $refresh->setType('action');
      $refresh->setSubType('other');
      $refresh->setLogicalId('refresh');
      $refresh->setName(__('Rafraîchir', __FILE__));
      $refresh->save();
    }

    // 4) Rafraîchissement auto si signe renseigné
    if ($signe !== '') {
      try {
        $this->refreshAll();
      } catch (Throwable $e) {
        log::add('horoscopeplus', 'error', 'postSave refresh: ' . $e->getMessage());
      }
    }
  }

  public function refreshAll(): array {
    $type  = strtolower($this->getConfiguration('type', 'chinois'));
    $signe = strtolower($this->getConfiguration('signe', ''));

    if ($signe === '') {
      return [];
    }

    // Normalise la date au format "15 Mars 2026"
    // Stratégie : regex française en premier — jamais de strtotime() sur une date française
    // car PHP peut décaler d'un jour selon la timezone.
    $normalizeDate = function(string $raw): string {
      $raw = trim($raw);

      $moisFrNum = [
        'janvier'=>1,'février'=>2,'fevrier'=>2,'mars'=>3,'avril'=>4,'mai'=>5,'juin'=>6,
        'juillet'=>7,'août'=>8,'aout'=>8,'septembre'=>9,'octobre'=>10,'novembre'=>11,'décembre'=>12,'decembre'=>12
      ];
      $moisNumFr = [
        1=>'Janvier',2=>'Février',3=>'Mars',4=>'Avril',5=>'Mai',6=>'Juin',
        7=>'Juillet',8=>'Août',9=>'Septembre',10=>'Octobre',11=>'Novembre',12=>'Décembre'
      ];

      // Pattern : "15 Mars 2026" ou "lundi 15 mars 2026" ou "15 mars 2026"
      // On teste sur la version lowercase et sur la version ASCII (pour les accents)
      $rawLow   = mb_strtolower($raw, 'UTF-8');
      $rawAscii = @iconv('UTF-8', 'ASCII//TRANSLIT', $rawLow);
      $pattern  = '/\b(\d{1,2})\s+(janvier|f[eé]vrier|mars|avril|mai|juin|juillet|ao[uû]t|septembre|octobre|novembre|d[eé]cembre)\s+(\d{4})\b/iu';

      $matched = preg_match($pattern, $rawLow, $regs) || preg_match($pattern, $rawAscii, $regs);
      if ($matched) {
        $jour   = intval($regs[1]);
        $moisK  = @iconv('UTF-8', 'ASCII//TRANSLIT', mb_strtolower($regs[2], 'UTF-8'));
        $annee  = intval($regs[3]);
        $moisN  = $moisFrNum[$moisK] ?? null;
        if ($moisN !== null) {
          return $jour . ' ' . $moisNumFr[$moisN] . ' ' . $annee;
        }
      }

      // Fallback uniquement pour les formats non-français (ISO, anglais)
      $hasFrMois = false;
      foreach (array_keys($moisFrNum) as $frMois) {
        if (mb_stripos($rawLow, $frMois) !== false) { $hasFrMois = true; break; }
      }
      if (!$hasFrMois) {
        $ts = strtotime($raw);
        if ($ts !== false) {
          $moisEn = ['January'=>'Janvier','February'=>'Février','March'=>'Mars','April'=>'Avril',
                     'May'=>'Mai','June'=>'Juin','July'=>'Juillet','August'=>'Août',
                     'September'=>'Septembre','October'=>'Octobre','November'=>'Novembre','December'=>'Décembre'];
          return str_replace(array_keys($moisEn), array_values($moisEn), date('j F Y', $ts));
        }
      }

      // Dernier recours : retourner tel quel (déjà propre ou format inconnu)
      return $raw;
    };

    if ($type === 'chinois') {
      $data = self::fetchChinois($signe);

      $this->updateInfoCmd('date', $normalizeDate($data['date'] ?? ''));
      $this->updateInfoCmd('logo', $data['logo'] ?? '');

      if (isset($data['themes']) && is_array($data['themes'])) {
        foreach ($data['themes'] as $theme => $value) {
          $logicalId = self::logicalIdFromTheme($theme);
          $this->updateInfoCmd($logicalId, (string)$value);
        }
      }

      return $data;
    }

    if ($type === 'occidental') {
      $data = self::fetchOccidental($signe);

      $this->updateInfoCmd('date', $normalizeDate($data['date'] ?? ''));
      $this->updateInfoCmd('logo', $data['logo'] ?? '');

      if (isset($data['themes']) && is_array($data['themes'])) {
        foreach ($data['themes'] as $theme => $value) {
          $logicalId = self::logicalIdFromTheme($theme);
          $this->updateInfoCmd($logicalId, (string)$value);
        }
      }

      if (isset($data['decans']) && is_array($data['decans'])) {
        foreach ($data['decans'] as $theme => $value) {
          $logicalId = self::logicalIdFromTheme($theme);
          $this->updateInfoCmd($logicalId, (string)$value);
        }
      }

      return $data;
    }

    throw new Exception('Type inconnu: ' . $type);
  }

  private function updateInfoCmd(string $logicalId, $value): void {
    $cmd = $this->getCmd('info', $logicalId);
    if (!is_object($cmd)) {
      $cmd = new horoscopeplusCmd();
      $cmd->setEqLogic_id($this->getId());
      $cmd->setType('info');
      $cmd->setSubType('string');
      $cmd->setLogicalId($logicalId);
      $cmd->setName($logicalId);
      $cmd->save();
    }
    $cmd->event($value === null ? '' : (string)$value);
  }

  public static function logicalIdFromTheme(string $theme): string {
    $theme = trim($theme);

    $map = [
      'Date'       => 'date',
      'Logo'       => 'logo',

      'Humeur'     => 'humeur',
      'Amour'      => 'amour',
      'Argent'     => 'argent',
      'Travail'    => 'travail',
      'Loisirs'    => 'loisirs',

      'Bien-être'  => 'bien_etre',
      'Famille'    => 'famille',
      'Relation'   => 'relation',

      '1er Décan'  => 'decan1',
      '2ème Décan' => 'decan2',
      '3ème Décan' => 'decan3',

      // compat
      'Décan1'     => 'decan1',
      'Décan2'     => 'decan2',
      'Décan3'     => 'decan3',
    ];

    if (isset($map[$theme])) {
      return $map[$theme];
    }

    $t = mb_strtolower($theme, 'UTF-8');
    $t = @iconv('UTF-8', 'ASCII//TRANSLIT', $t);
    $t = preg_replace('/[^a-z0-9]+/', '_', $t);
    $t = trim($t, '_');

    return ($t === '') ? 'theme' : $t;
  }

  public static function fetchChinois(string $signe): array {
    $signe = strtolower(trim($signe));
    $valid = ['rat','boeuf','tigre','lapin','lievre','dragon','serpent','cheval','chevre','singe','coq','chien','cochon'];
    // Substitution : lapin est un alias de lièvre (le site utilise lievre dans l'URL)
    if ($signe === 'lapin') $signe = 'lievre';
    if (!in_array($signe, $valid, true)) {
      throw new Exception("Signe chinois invalide: {$signe}");
    }

    $url = "https://www.mon-horoscope-du-jour.com/horoscopes-chinois/quotidien/{$signe}.htm";
    log::add('horoscopeplus', 'debug', 'URL chinois générée : ' . $url);

    libxml_use_internal_errors(true);
    $html = @file_get_contents($url);
    if ($html === false || trim($html) === '') {
      throw new Exception("Impossible de récupérer la page: {$url}");
    }

    $dom = new DOMDocument();
    $dom->loadHTML($html);
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);

    $titleNodes = $xpath->query("//div[contains(@class,'summary-title')]");
    $titles = [];
    foreach ($titleNodes as $node) {
      $titles[] = trim($node->textContent);
    }

    $textNodes = $xpath->query("//div[contains(@class,'horoscope-text')]/p");
    $texts = [];
    foreach ($textNodes as $node) {
      $t = trim($node->textContent);
      $t = html_entity_decode($t, ENT_QUOTES | ENT_HTML5, 'UTF-8');
      $t = str_replace('&&', 'et', $t);
      $t = preg_replace('/&\s*&/', 'et', $t);
      $t = preg_replace('/\s+/', ' ', $t);
      $texts[] = $t;
    }

    $themes = [];
    $max = min(count($titles), count($texts));
    for ($i = 0; $i < $max; $i++) {
      if ($titles[$i] !== '' && isset($texts[$i])) {
        $themes[$titles[$i]] = $texts[$i];
      }
    }

    $date = "Date non trouvée";
    $dateNode = $xpath->query("//div[contains(@class,'mobile-menu-saint')]");
    if ($dateNode->length > 0) {
      $raw = trim($dateNode->item(0)->textContent);
      $parts = explode("|", $raw);
      $raw = trim($parts[0]);
      // Supprimer le nom du jour en tête ("Dimanche ", "Lundi ", etc.)
      $raw = preg_replace('/^\s*(lundi|mardi|mercredi|jeudi|vendredi|samedi|dimanche)\s+/iu', '', $raw);
      $date = trim($raw);
    }

    // Logo chinois : même convention de numérotation que l'occidental
    $signeLogoMap = [
      'rat'=>0,'boeuf'=>1,'tigre'=>2,'lapin'=>3,'lievre'=>3,'dragon'=>4,'serpent'=>5,
      'cheval'=>6,'chevre'=>7,'singe'=>8,'coq'=>9,'chien'=>10,'cochon'=>11
    ];
    $logoNum = $signeLogoMap[$signe] ?? null;
    $logo = $logoNum !== null
      ? 'https://www.mon-horoscope-du-jour.com/images/vectors/sign_ch_color_' . $logoNum . '.png'
      : '';

    log::add('horoscopeplus', 'debug', 'fetchChinois OK : ' . $signe . ' | date=' . $date . ' | themes=' . implode(',', array_keys($themes)));
    return [
      'type'   => 'chinois',
      'signe'  => $signe,
      'url'    => $url,
      'date'   => $date,
      'logo'   => $logo,
      'themes' => $themes
    ];
  }

  /**
   * Occidental : parsing robuste basé sur les sections horoscope et décan.
   */
  public static function fetchOccidental(string $signe): array {
    // Normalisation (accents / espaces)
    $signe = strtolower(trim($signe));
    $signe = @iconv('UTF-8', 'ASCII//TRANSLIT', $signe);
    $signe = preg_replace('/[^a-z]/', '', $signe);

    $valid = ['belier','taureau','gemeaux','cancer','lion','vierge','balance','scorpion','sagittaire','capricorne','verseau','poissons'];
    if (!in_array($signe, $valid, true)) {
      throw new Exception("Signe occidental invalide: {$signe}");
    }

    $url = "https://www.mon-horoscope-du-jour.com/horoscopes/quotidien/{$signe}.htm";
    log::add('horoscopeplus', 'debug', 'URL occidental générée : ' . $url);

    libxml_use_internal_errors(true);
    $html = @file_get_contents($url);
    if ($html === false || trim($html) === '') {
      throw new Exception("Impossible de récupérer la page: {$url}");
    }

    $dom = new DOMDocument();
    $dom->loadHTML($html);
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);

    // Date depuis H1 si possible
    $date = "Date non trouvée";
    $h1 = $xpath->query('//h1');
    if ($h1->length > 0) {
      $h1txt = trim($h1->item(0)->textContent);
      if (preg_match("/aujourd'hui,\s*(.+)$/i", $h1txt, $m)) {
        // Supprimer le nom du jour en tête ("lundi ", "mardi ", etc.)
        $raw = trim($m[1]);
        $raw = preg_replace('/^\s*(lundi|mardi|mercredi|jeudi|vendredi|samedi|dimanche)\s+/iu', '', $raw);
        $date = trim($raw);
      }
    }

    // Logo : URL construite directement depuis le numéro du signe (plus de parsing HTML)
    $signeLogoMap = [
      'belier'=>0,'taureau'=>1,'gemeaux'=>2,'cancer'=>3,'lion'=>4,'vierge'=>5,
      'balance'=>6,'scorpion'=>7,'sagittaire'=>8,'capricorne'=>9,'verseau'=>10,'poissons'=>11
    ];
    $logoNum = $signeLogoMap[$signe] ?? null;
    $logo = $logoNum !== null
      ? 'https://www.mon-horoscope-du-jour.com/images/vectors/sign_color_' . $logoNum . '.png'
      : '';

    // Helper : nettoie le textContent d'un noeud
    $cleanText = function($node): string {
      $t = trim(html_entity_decode($node->textContent, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
      return trim(preg_replace('/\s+/', ' ', $t));
    };

    // THEMES : chaque div.horoscope-section contient un div.horoscope-card//h2 (titre)
    // et un div.horoscope-text (texte), dans cet ordre
    $themes = [];
    $sections = $xpath->query('//div[contains(@class,"horoscope-section")]');
    foreach ($sections as $section) {
      $h2nodes = $xpath->query('.//div[contains(@class,"horoscope-card")]//h2', $section);
      $txtNodes = $xpath->query('.//div[contains(@class,"horoscope-text")]', $section);
      if ($h2nodes->length > 0 && $txtNodes->length > 0) {
        $title = trim($h2nodes->item(0)->textContent);
        $themes[$title] = $cleanText($txtNodes->item(0));
      }
    }

    // DECANS : chaque div.decan-content contient un div.decan-card//h2 (titre)
    // et un div.horoscope-text (texte)
    $decans = ['1er Décan' => '', '2ème Décan' => '', '3ème Décan' => ''];
    $decanSections = $xpath->query('//div[contains(@class,"decan-content")]');
    foreach ($decanSections as $section) {
      $h2nodes  = $xpath->query('.//div[contains(@class,"decan-card")]//h2', $section);
      $txtNodes = $xpath->query('.//div[contains(@class,"horoscope-text")]', $section);
      if ($h2nodes->length > 0 && $txtNodes->length > 0) {
        $title = trim($h2nodes->item(0)->textContent);
        if (isset($decans[$title])) {
          $decans[$title] = $cleanText($txtNodes->item(0));
        }
      }
    }

    log::add('horoscopeplus', 'debug', 'fetchOccidental OK : ' . $signe . ' | date=' . $date . ' | themes=' . implode(',', array_keys($themes)) . ' | decans=' . implode(',', array_keys(array_filter($decans, function($v) { return $v !== ''; }))));
    return [
      'type'   => 'occidental',
      'signe'  => $signe,
      'url'    => $url,
      'date'   => $date,
      'logo'   => $logo,
      'themes' => $themes,
      'decans' => $decans
    ];
  }


  /**
   * Appelé toutes les minutes par Jeedom.
   * - Si un cron personnalisé est défini dans la config : on vérifie l'expression et on refresh si due.
   * - Sinon : comportement cronDaily (refresh une fois par jour à 02:30).
   */
  public static function cron() {
    $cronExpr = trim(config::byKey('cron_refresh', 'horoscopeplus', ''));

    if ($cronExpr !== '') {
      // Cron personnalisé
      try {
        $c = new Cron\CronExpression(checkAndFixCron($cronExpr), new Cron\FieldFactory);
        if (!$c->isDue()) {
          return;
        }
      } catch (Throwable $e) {
        log::add('horoscopeplus', 'error', 'Expression cron invalide : ' . $cronExpr . ' => ' . $e->getMessage());
        return;
      }
      log::add('horoscopeplus', 'info', 'cron personnalisé déclenché (' . $cronExpr . ') à ' . date('H:i:s'));
    } else {
      // Pas de cron perso : on se comporte comme cronDaily (00:00)
      if (date('H:i') !== '02:30') {
        return;
      }
      log::add('horoscopeplus', 'info', 'cron quotidien (défaut 02:30) démarré à ' . date('H:i:s'));
    }

    self::refreshAllEqLogics('cron');
  }

  private static function refreshAllEqLogics(string $caller = 'cron') {
    foreach (eqLogic::byType('horoscopeplus') as $eq) {
      try {
        /** @var horoscopeplus $eq */
        if (!$eq->getIsEnable()) continue;
        log::add('horoscopeplus', 'info', $caller . ': refresh ' . $eq->getHumanName() . ' (' . $eq->getConfiguration('type', '?') . ')');
        $eq->refreshAll();
        log::add('horoscopeplus', 'info', $caller . ': OK ' . $eq->getHumanName());
      } catch (Throwable $e) {
        log::add('horoscopeplus', 'error', $caller . ': ' . $eq->getHumanName() . ' => ' . $e->getMessage());
      }
    }
  }
}

class horoscopeplusCmd extends cmd {

  public function execute($_options = array()) {
    $eqLogic = $this->getEqLogic();
    if (!is_object($eqLogic)) {
      throw new Exception('Aucun équipement associé à cette commande');
    }

    if ($this->getType() === 'action' && $this->getLogicalId() === 'refresh') {
      $eqLogic->refreshAll();
      return true;
    }

    // Mise à jour uniquement via "Rafraîchir"
    return null;
  }
}
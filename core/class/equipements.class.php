<?php

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';

class equipements extends eqLogic {
	/*     * *************************Attributs****************************** */
	
	/*     * ***********************Methode static*************************** */
	
	public static function event() {
		
	}
	
	public static function cron() {
		foreach (eqLogic::byType('equipements', true) as $eqLogic) {
			$autorefresh = $eqLogic->getConfiguration('autorefresh');
			if ($autorefresh != '') {
				try {
					$c = new Cron\CronExpression(checkAndFixCron($autorefresh), new Cron\FieldFactory);
					if ($c->isDue()) {
						$eqLogic->refresh();
					}
				} catch (Exception $exc) {
					log::add('equipements', 'error', __('Expression cron non valide pour ', __FILE__) . $eqLogic->getHumanName() . ' : ' . $autorefresh);
				}
			}
		}
	}
	
	public static function templateParameters($_template = ''){
		$return = array();
		foreach (ls(dirname(__FILE__) . '/../config/template', '*.json', false, array('files', 'quiet')) as $file) {
			try {
				$content = file_get_contents(dirname(__FILE__) . '/../config/template/' . $file);
				if (is_json($content)) {
					$return += json_decode($content, true);
				}
			} catch (Exception $e) {
				
			}
		}
		if (isset($_template) && $_template != '') {
			if (isset($return[$_template])) {
				return $return[$_template];
			}
			return array();
		}
		return $return;
	}
	
	public static function deadCmd() {

	}
	
	/*     * *********************Methode d'instance************************* */
	public function applyTemplate($_template){
		$template = self::templateParameters($_template);
		if (!is_array($template)) {
			return true;
		}
		$this->import($template);
	}
	
	public function refresh() {
		try {
			foreach ($this->getCmd('info') as $cmd) {
				if ($cmd->getConfiguration('calcul') == '' || $cmd->getConfiguration('equipementsAction', 0) != '0') {
					continue;
				}
				$value = $cmd->execute();
				if ($cmd->execCmd() != $cmd->formatValue($value)) {
					$cmd->event($value);
				}
			}
		} catch (Exception $exc) {
			log::add('equipements', 'error', __('Erreur pour ', __FILE__) . $eqLogic->getHumanName() . ' : ' . $exc->getMessage());
		}
	}
	
	public function postSave() {
		$createRefreshCmd = true;
		$refresh = $this->getCmd(null, 'refresh');
		if (!is_object($refresh)) {
			$refresh = cmd::byEqLogicIdCmdName($this->getId(), __('Rafraichir', __FILE__));
			if (is_object($refresh)) {
				$createRefreshCmd = false;
			}
		}
		if ($createRefreshCmd) {
			if (!is_object($refresh)) {
				$refresh = new equipementsCmd();
				$refresh->setLogicalId('refresh');
				$refresh->setIsVisible(1);
				$refresh->setName(__('Rafraichir', __FILE__));
			}
			$refresh->setType('action');
			$refresh->setSubType('other');
			$refresh->setEqLogic_id($this->getId());
			$refresh->save();
		} 
      
	}
  
  	/*     * ***********Systeme de template pour les widgets***************** */
  	public static function templateWidget(){
      
    $return = array('action' => array('string' => array()));
	$return['action']['other']['Lum_ON_OFF'] = array(
		'template' => 'tmplicon',
		'replace' => array(
			'#_icon_on_#' => '<img src=\'plugins/equipements/core/template/images/LumON.png\'>',
			'#_icon_off_#' => '<img src=\'plugins/equipements/core/template/images/LumOFF.png\'>'
			)
	);
    $return['action']['other']['ON_OFF'] = array(
		'template' => 'tmplicon',
		'replace' => array(
			'#_icon_on_#' => '<img src=\'plugins/equipements/core/template/images/ToggleCircle_ON.png\'>',
			'#_icon_off_#' => '<img src=\'plugins/equipements/core/template/images/ToggleCircle_OFF.png\'>'
			)
	);
    $return['action']['other']['MANU_AUTO'] = array(
		'template' => 'tmplicon',
		'replace' => array(
			'#_icon_on_#' => '<img src=\'plugins/equipements/core/template/images/AUTO.png\' style=\'width:65px;height:28px;\'>',
			'#_icon_off_#' => '<img src=\'plugins/equipements/core/template/images/MANU.png\' style=\'width:65px;height:28px;\'>'
			)
	);
    $return['action']['other']['Porte'] = array(
		'template' => 'tmplicon',
		'replace' => array(
			'#_icon_on_#' => '<img src=\'plugins/equipements/core/template/images/DoorOpen.png\' >',
			'#_icon_off_#' => '<img src=\'plugins/equipements/core/template/images/DoorClose.png\' >'
			)
	);  
    $return['info']['binary']['Presence_Homme'] = array(
		'template' => 'tmplicon',
		'replace' => array(
			'#_icon_on_#' => '<img src=\'plugins/equipements/core/template/images/icon_homme_prs.png\' >',
			'#_icon_off_#' => '<img src=\'plugins/equipements/core/template/images/icon_homme_abs.png\' >'
			)
	);
    $return['info']['binary']['Presence_Femme'] = array(
		'template' => 'tmplicon',
		'replace' => array(
			'#_icon_on_#' => '<img src=\'plugins/equipements/core/template/images/icon_femme_prs.png\' >',
			'#_icon_off_#' => '<img src=\'plugins/equipements/core/template/images/icon_femme_abs.png\' >'
			)
	);
    $return['info']['binary']['Presence_Garcon'] = array(
		'template' => 'tmplicon',
		'replace' => array(
			'#_icon_on_#' => '<img src=\'plugins/equipements/core/template/images/icon_garcon_prs.png\' >',
			'#_icon_off_#' => '<img src=\'plugins/equipements/core/template/images/icon_garcon_abs.png\' >'
			)
	);
    $return['info']['binary']['Presence_Fille'] = array(
		'template' => 'tmplicon',
		'replace' => array(
			'#_icon_on_#' => '<img src=\'plugins/equipements/core/template/images/icon_fille_prs.png\' >',
			'#_icon_off_#' => '<img src=\'plugins/equipements/core/template/images/icon_fille_abs.png\' >'
			)
	);
    $return['info']['binary']['Info_Binaire'] = array(
		'template' => 'tmplicon',
		'replace' => array(
			'#_icon_on_#' => '<img src=\'plugins/equipements/core/template/images/EnCours.png\' >',
			'#_icon_off_#' => '<img src=\'plugins/equipements/core/template/images/Arret.png\' >'
			)
	);

	$return['info']['numeric']['Volet'] = array(
		'template' => 'tmplmultistate',
		'test' => array(
			array('operation' => '#value# == 0','state_light' => '<img src=\'plugins/equipements/core/template/images/Store-0.png\' >'),
          	array('operation' => '#value# > 0 && #value# <= 35','state_light' => '<img src=\'plugins/equipements/core/template/images/Store-10.png\' >'),
          	array('operation' => '#value# > 35 && #value# <= 45','state_light' => '<img src=\'plugins/equipements/core/template/images/Store-20.png\' >'),
          	array('operation' => '#value# > 45 && #value# <= 55','state_light' => '<img src=\'plugins/equipements/core/template/images/Store-30.png\' >'),
          	array('operation' => '#value# > 55 && #value# <= 63','state_light' => '<img src=\'plugins/equipements/core/template/images/Store-40.png\' >'),
          	array('operation' => '#value# > 63 && #value# <= 73','state_light' => '<img src=\'plugins/equipements/core/template/images/Store-50.png\' >'),
          	array('operation' => '#value# > 73 && #value# <= 84','state_light' => '<img src=\'plugins/equipements/core/template/images/Store-60.png\' >'),
          	array('operation' => '#value# > 84 && #value# <= 92','state_light' => '<img src=\'plugins/equipements/core/template/images/Store-70.png\' >'),
          	array('operation' => '#value# > 92 && #value# <= 95','state_light' => '<img src=\'plugins/equipements/core/template/images/Store-80.png\' >'),
          	array('operation' => '#value# > 95','state_light' => '<img src=\'plugins/equipements/core/template/images/Store-99.png\' >')
		)
	);
      
    $return['action']['other']['Garage'] = array(
		'template' => 'tmplicon',
		'replace' => array(
			'#_icon_on_#' => '<img src=\'plugins/equipements/core/template/images/garage_on.png\' >',
			'#_icon_off_#' => '<img src=\'plugins/equipements/core/template/images/garage_off.png\' >'
			)
	);
      
    $return['action']['other']['Portail'] = array(
		'template' => 'tmplicon',
		'replace' => array(
			'#_icon_on_#' => '<img src=\'plugins/equipements/core/template/images/Portail-100.png\' >',
			'#_icon_off_#' => '<img src=\'plugins/equipements/core/template/images/Portail-00.png\' >'
			)
	);
      
    $return['action']['message']['Horaire'] = array(
      	'template' => 'horaire'
	);
    
    $return['info']['numeric']['Info_Numeric'] = array(
      	'template' => 'num'
	);  
      
    $return['info']['numeric']['Thermometreflat'] = array(
      	'template' => 'thermometreflat'
	); 
      
    $return['info']['numeric']['Humiditeflat'] = array(
      	'template' => 'humiditeflat'
	);
      
	return $return;
      
	}
  
  
	

	
	/*     * **********************Getteur Setteur*************************** */
}

class equipementsCmd extends cmd {
	/*     * *************************Attributs****************************** */
	
	/*     * ***********************Methode static*************************** */
	
	/*     * *********************Methode d'instance************************* */
	
	public function dontRemoveCmd() {
		if ($this->getLogicalId() == 'refresh') {
			return true;
		}
		return false;
	}
	
	public function preSave() {
		if ($this->getLogicalId() == 'refresh') {
			return;
		}
		if ($this->getConfiguration('equipementsAction') == 1) {
			$actionInfo = equipementsCmd::byEqLogicIdCmdName($this->getEqLogic_id(), $this->getName());
			if (is_object($actionInfo)) {
				$this->setId($actionInfo->getId());
			}
			$this->setConfiguration('calcul','');
			if($this->getType() == 'info'){
				$this->setValue('');
			}
		}
		if ($this->getType() == 'action') {
			if ($this->getConfiguration('infoName') == '') {
				throw new Exception(__('Le nom de la commande info ne peut etre vide', __FILE__));
			}
			$cmd = cmd::byId(str_replace('#', '', $this->getConfiguration('infoName')));
			if (is_object($cmd)) {
				if($cmd->getId() == $this->getId()){
					throw new Exception(__('Vous ne pouvez appeller la commande elle meme (boucle infinie) sur : ', __FILE__).$this->getName());
				}
				$this->setSubType($cmd->getSubType());
				$this->setConfiguration('infoId', '');
			} else {
				$actionInfo = equipementsCmd::byEqLogicIdCmdName($this->getEqLogic_id(), $this->getConfiguration('infoName'));
				if (!is_object($actionInfo)) {
					$actionInfo = new equipementsCmd();
					$actionInfo->setType('info');
					switch ($this->getSubType()) {
						case 'slider':
						$actionInfo->setSubType('numeric');
						break;
						default:
						$actionInfo->setSubType('string');
						break;
					}
				}else{
					if($actionInfo->getId() == $this->getId()){
						throw new Exception(__('Vous ne pouvez appeller la commande elle meme (boucle infinie) sur : ', __FILE__).$this->getName());
					}
				}
				$actionInfo->setConfiguration('equipementsAction', 1);
				$actionInfo->setName($this->getConfiguration('infoName'));
				$actionInfo->setEqLogic_id($this->getEqLogic_id());
				$actionInfo->save();
				$this->setConfiguration('infoId', $actionInfo->getId());
			}
		} else {
			$calcul = $this->getConfiguration('calcul');
			if (strpos($calcul, '#' . $this->getId() . '#') !== false) {
				throw new Exception(__('Vous ne pouvez faire un calcul sur la valeur elle meme (boucle infinie) : ', __FILE__).$this->getName());
			}
			preg_match_all("/#([0-9]*)#/", $calcul, $matches);
			$value = '';
			foreach ($matches[1] as $cmd_id) {
				if (is_numeric($cmd_id)) {
					$cmd = self::byId($cmd_id);
					if (is_object($cmd) && $cmd->getType() == 'info') {
						$value .= '#' . $cmd_id . '#';
					}
				}
			}
			preg_match_all("/variable\((.*?)\)/", $calcul, $matches);
			foreach ($matches[1] as $variable) {
				$value .= '#variable(' . $variable . ')#';
			}
			$this->setValue($value);
		}
	}
	
	public function postSave() {
		if ($this->getType() == 'info' && $this->getConfiguration('equipementsAction', 0) == '0' && $this->getConfiguration('calcul') != '') {
			$this->event($this->execute());
		}
	}
	
	public function execute($_options = null) {
		$eqLogic = $this->getEqLogic();
		if ($this->getLogicalId() == 'refresh') {
			$eqLogic->refresh();
			return;
		}
		switch ($this->getType()) {
			case 'info':
			if ($this->getConfiguration('equipementsAction', 0) == '0') {
				try {
					$result = jeedom::evaluateExpression($this->getConfiguration('calcul'));
					if(is_string($result)){
						$result = str_replace('"', '', $result);
					}
					return $result;
				} catch (Exception $e) {
					log::add('equipements', 'info', $e->getMessage());
					return $this->getConfiguration('calcul');
				}
			}
			break;
			case 'action':
			$equipementsCmd = equipementsCmd::byId($this->getConfiguration('infoId'));
			if (!is_object($equipementsCmd)) {
				$cmds = explode('&&', $this->getConfiguration('infoName'));
				if (is_array($cmds)) {
					foreach ($cmds as $cmd_id) {
						$cmd = cmd::byId(str_replace('#', '', $cmd_id));
						if (is_object($cmd)) {
							try {
								$cmd->execCmd($_options);
							} catch (\Exception $e) {
								
							}
						}
					}
					return;
				} else {
					$cmd = cmd::byId(str_replace('#', '', $this->getConfiguration('infoName')));
					return $cmd->execCmd($_options);
				}
			} else {
				if ($equipementsCmd->getEqType() != 'equipements') {
					throw new Exception(__('La cible de la commande n\'est pas un équipement de type equipements', __FILE__));
				}
				if ($this->getSubType() == 'slider') {
					$value = $_options['slider'];
				} else if ($this->getSubType() == 'color') {
					$value = $_options['color'];
				} else if ($this->getSubType() == 'select') {
					$value = $_options['select'];
				} else {
					$value = $this->getConfiguration('value');
				}
				$result = jeedom::evaluateExpression($value);
				if ($this->getSubtype() == 'message') {
					$result = $_options['title'] . ' ' . $_options['message'];
				}
				$eqLogic->checkAndUpdateCmd($equipementsCmd,$result);
			}
			break;
		}
	}
	
	/*     * **********************Getteur Setteur*************************** */
}

?>
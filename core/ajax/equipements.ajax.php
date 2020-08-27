<?php

try {
  require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
  include_file('core', 'authentification', 'php');
  
  if (!isConnect('admin')) {
    throw new Exception(__('401 - Accès non autorisé', __FILE__));
  }
  
  if (init('action') == 'copyFromEqLogic') {
    $equipements = equipements::byId(init('id'));
    if (!is_object($equipements)) {
      throw new Exception(__('Equipement introuvable : ', __FILE__) . init('id'));
    }
    $equipements->copyFromEqLogic(init('eqLogic_id'));
    ajax::success();
  }
  
  if (init('action') == 'getTemplateList') {
    ajax::success(equipements::templateParameters());
  }
  
  if (init('action') == 'applyTemplate') {
    $equipements = equipements::byId(init('id'));
    if (!is_object($equipements)) {
      throw new Exception(__('Equipement introuvable : ', __FILE__) . init('id'));
    }
    $equipements->applyTemplate(init('name'));
    ajax::success();
  }
  
  throw new Exception(__('Aucune methode correspondante à : ', __FILE__) . init('action'));
  /*     * *********Catch exeption*************** */
} catch (Exception $e) {
  ajax::error(displayExeption($e), $e->getCode());
}
?>
/**
 * The campaigner subscription confirmation snippet
 *
 * @package Campaigner
 * @author Patrick Stummer <info@patrickstummer.com>
 */

$campaigner = $modx->getService('campaigner', 'Campaigner', $modx->getOption('core_path'). 'components/campaigner/model/campaigner/');
if (!($campaigner instanceof Campaigner)) return;

$tpl       = $modx->getOption('tpl', $scriptProperties, 'campaignerMessage');
$errorTpl  = $modx->getOption('errorTpl', $scriptProperties, $tpl);

$get = $modx->request->getParameters();

if(empty($get['subscriber'])) return;

if($campaigner->confirm($get['subscriber'], $get['key'])) {
    $params['message'] = $modx->lexicon('campaigner.confirm.success');
    $params['type']    = 'success';
    return $modx->getChunk($tpl, $params);
} else {
    $params['message'] = $campaigner->errormsg[0];
    $params['type']    = 'error';
    return $modx->getChunk($errorTpl, $params);
}
<?php
// error_reporting(-1);
/**
 * Get list of files
 */

$files = glob($this->modx->getOption('core_path') . 'cache/logs/campaigner/*');
foreach($files as $file) {
	$list[]['filename'] = $file;
}
return $this->modx->toJSON($list);

// require_once MODX_CORE_PATH.'model/modx/processors/browser/directory/getlist.class.php';

// class LogfileListProcessor extends modBrowserFolderGetListProcessor {

//     public function initialize() {
//         // $this->setDefaultProperties(array(
//         //     'id' => '',
//         // ));
//         $this->setProperty('source', 0);
//         $this->setProperty('id', $this->modx->getOption('core_path') . 'cache/logs/campaigner/');
//         return true;
//     }

//     public function process() {
//         if (!$this->getSource()) {
//             return $this->modx->toJSON(array());
//         }
//         if (!$this->source->checkPolicy('list')) {
//             return $this->modx->toJSON(array());
//         }
//         $this->source->setRequestProperties($this->getProperties());
//         $this->source->initialize();
//         var_dump($this->getProperties());
//         $list = $this->source->getContainerList($this->getProperty('dir'));
//         return $this->modx->toJSON($list);
//     }
// }
// return 'LogfileListProcessor';
<?php
class ARNewsletterGetListProcessor extends modObjectGetListProcessor {
    public $classKey = 'Newsletter';
    public $languageTopics = array('campaigner:default');
    public $defaultSortField = 'menuindex';
    public $defaultSortDirection = 'ASC';
    public $objectType = 'campaigner.fields';

    public function prepareQueryBeforeCount(xPDOQuery $c) {
    	$c->leftJoin('modDocument', 'Document', '`Document`.`id` = `Newsletter`.`docid`');
        $c->select(array(
            'id' => $this->modx->getSelectColumns('Newsletter','Newsletter','',array('id')),
            'pagetitle' => '`Document`.`pagetitle`',
        ));
        $c->where(array(
            'state' => 1,
            'sent_date:!=' => NULL
            )
        );
        // $c->prepare();
        // echo $c->toSQL();
    	return $c;
    }

    public function prepareRow(xPDOObject $object) {
        $ta = $object->toArray('', false, true);
        $arr = array();
        $arr['value'] = $ta['id'];
        $arr['display'] = $ta['pagetitle'];
        return $arr;
    }
}
return 'ARNewsletterGetListProcessor';
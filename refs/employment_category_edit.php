<?php

#####################################################################
#
# Травмпункт. (c) 2005 Vista
#
#####################################################################

require_once 'library/users.php';

class TEditor extends HTML_QuickFormEx
{
    function TEditor($AId='')
    {
        $vDB = GetDB();
        if ( !empty($AId) )
        {
            $vRecord = $vDB->GetById('rb_employment_categories', $AId);
            if (  !is_array($vRecord) )
               $vRecord = array();
        }
        else
            $vRecord = array();


        $this->HTML_QuickForm('frmItem', 'post', $_SERVER['REQUEST_URI']);
        $this->setMyRequiredNote();
        $this->addElement('header',   'header',          'Категория');
        $this->addElement('hidden',   'id',              '');
        $this->addElement('text',     'name',            'Наименование',   array('class'=>'edt_avg'));
        $this->addElement('checkbox', 'need_ill_doc',    'Нужен б/л или справка');
        $this->addElement('submit',   'Submit',     'Ok');
        $this->applyFilter('_ALL_', 'trim');

        $this->addRule('name', 'Это поле обязательно для заполнения', 'required');
        $this->setDefaults($vRecord);
    }

    function Save()
    {
        if ( !$this->validate() )
            return FALSE;

        $vDB = GetDB();
        $vValues = $this->getSubmitValues(TRUE);
//            $vId = $vValues['id'];
        $vRecord = array();

        if ( !empty($vValues['id']) )
          $vRecord['id'] = $vValues['id'];
        CopyRecordStrValue($vRecord, $vValues, 'name');
        CopyRecordBoolValue($vRecord, $vValues, 'need_ill_doc');
        $vResult = $vDB->InsertOrUpdateById('rb_employment_categories', $vRecord);


       return $vResult;
    }

}


$vId = array_key_exists('id', $_GET) ? $_GET['id'] : '';
$vForm = new TEditor($vId);
if ( !$vForm->Save() )
{
    $vTemplate =& CreateTemplate();
    $vRenderer =& CreateRenderer($vTemplate);
    $vForm->accept($vRenderer);
    $vView =& new TBaseView();
    $vView->form = $vRenderer->toObject();
    $vTemplate->compile('refs/employment_category_edit.html');
    $vTemplate->outputObject($vView);
}
else
{
    RedirectToList('employment_categories.html');
}

?>
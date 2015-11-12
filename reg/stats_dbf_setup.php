<?php

#####################################################################
#
# Травмпункт. (c) 2005 Vista
#
#####################################################################

require_once('config/config.php');
require_once('library/cases_table.php');


class TSearchForm extends HTML_QuickFormEx
{
    function TSearchForm()
    {
        $this->HTML_QuickForm('frmSearch', 'post', $_SERVER['REQUEST_URI']);
        $this->addElement('header',   'Header',          'Период');

        $this->addElement('dateex',   'beg_date',  'Начальная дата',    array('language' => 'ru', 'format'=>'dMY', 'minYear'=>gMinYear, 'maxYear'=>gMaxYear, 'addEmptyOption'=>false));
        $this->addElement('dateex',   'end_date',  'Конечная дата',     array('language' => 'ru', 'format'=>'dMY', 'minYear'=>gMinYear, 'maxYear'=>gMaxYear, 'addEmptyOption'=>false));
//         $this->addElement('checkbox', 'reexpose',  'Перевыставлять');
	$this->addElement('text',     'service',   'Профиль',           array('class'=>'edt_tiny'));
    $this->addElement('select',   'paytype', 'Тип', array(0=>'ОМС', 1=>'ДМС', 2=>'Платно'));
	$this->addElement('submit',   'Submit',    'Подготовить');

        $this->applyFilter('_ALL_', 'trim');
	    
        $this->setDefaults($_GET);
    }
}


class TData extends TBaseView
{
}

// =======================================================================
if ( !array_key_exists('beg_date', $_GET) )
    $_GET['beg_date'] = date('Y-m-d');
if ( !array_key_exists('end_date', $_GET) )
    $_GET['end_date'] = date('Y-m-d');
if ( !array_key_exists('service', $_GET) )
    $_GET['service'] = defaultService;
if ( !array_key_exists('paytype', $_GET) )
    $_GET['paytype'] = 0;

RegisterListParams();
$vForm =& new TSearchForm();

if ( $vForm->validate() )
{
    $vValues  = $vForm->getSubmitValues();
    $vFilter = array();
    CopyRecordDateValue($vFilter, $vValues, 'beg_date');
    CopyRecordDateValue($vFilter, $vValues, 'end_date');
    CopyParam($vFilter, $vValues, 'reexpose');
    CopyParam($vFilter, $vValues, 'service');
    CopyParam($vFilter, $vValues, 'paytype');
    Redirect( CompoundURL('stats.dbf', $vFilter) );
}
else
{
    $vTemplate =& CreateTemplate();
    $vRenderer =& CreateRenderer($vTemplate);
    $vForm->accept($vRenderer);
    $vView =& new TData;
    $vView->form = $vRenderer->toObject();
    $vTemplate->compile('reg/stats_dbf_setup.html');
    $vTemplate->outputObject($vView);
}

?>
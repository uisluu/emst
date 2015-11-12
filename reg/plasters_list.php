<?php

  #####################################################################
  #
  # Травмпункт. (c) 2005 Vista
  #
  #####################################################################
    require_once('config/config.php');
    require_once('library/cases_table.php');


    function ConstructCaseQuery(&$ADB, $AParams)
    {
        $vFilter = array();

        $vTable = 'emst_cases '.
                  ' LEFT JOIN emst_surgeries   ON emst_cases.id = emst_surgeries.case_id'.
                  ' LEFT JOIN rb_manipulations ON emst_surgeries.manipulation_id = rb_manipulations.id';

        $vFilter[] = 'rb_manipulations.is_plaster != 0';
        if ( array_key_exists('beg_date', $AParams) )
           $vFilter[] = $ADB->CondGE('create_time', $AParams['beg_date']);
        if ( array_key_exists('end_date', $AParams) )
           $vFilter[] = $ADB->CondLT('create_time', DateAddDay($AParams['end_date']));

        $vFilter = implode(' AND ', $vFilter);
        $vOrder = 'emst_cases.id';
        return array($vTable, $vFilter, $vOrder);
    }



  class TSearchForm extends HTML_QuickFormEx
  {
    function TSearchForm()
    {
      $this->HTML_QuickForm('frmSearch', 'post', $_SERVER['REQUEST_URI']);
      $this->addElement('header',   'Header',          'Поиск');

      $this->addElement('dateex',   'beg_date',  'Начальная дата',    array('language' => 'ru', 'format'=>'dMY', 'minYear'=>gMinYear, 'maxYear'=>gMaxYear, 'addEmptyOption'=>false));
      $this->addElement('dateex',   'end_date',  'Конечная дата',     array('language' => 'ru', 'format'=>'dMY', 'minYear'=>gMinYear, 'maxYear'=>gMaxYear, 'addEmptyOption'=>false));
/*      $this->addElement('select',   'Order',           'Упорядочить по',
                        array_values( GetPropertiesSortOrder() ),
                        array('style'=>'WIDTH: 180px'));
*/
      $this->addElement('submit',   'Submit',       'Установить фильтр');

      $this->applyFilter('_ALL_', 'trim');
      $this->setDefaults($_GET);
    }
  }


    class TData extends TBaseView
    {
        function GetTable()
        {
            $vDB = GetDB();
            list($vTable, $vFilter, $vOrder) = ConstructCaseQuery($vDB, $_GET);

            $vTab =& new TCasesTableEx($vTable, 'emst_cases.*, emst_surgeries.manipulation_text, rb_manipulations.name as manipulation_name', $vFilter, $vOrder);

            $vTab->AddColumn('manipulation_name', 'Манипуляция');
            $vTab->AddColumn('manipulation_text', 'Описание');

            $vFilter = array();
            CopyRecordDateValue($vFilter, $_GET, 'beg_date');
            CopyRecordDateValue($vFilter, $_GET, 'end_date');
            $vTab->AddTableAction('печать',  CompoundURL('plasters_list.pdf', $vFilter));
            $vResult = $vTab->ProduceHTML($vDB, $_GET['PageIdx']+0, 20);
            return $vResult;
        }
    }

// =======================================================================
    if ( !array_key_exists('beg_date', $_GET) )
      $_GET['beg_date'] = date('Y-m-d');
    if ( !array_key_exists('end_date', $_GET) )
      $_GET['end_date'] = date('Y-m-d');

    RegisterListParams();
    $vForm =& new TSearchForm();

    if ( $vForm->validate() )
    {
        $vValues  = $vForm->getSubmitValues();
        $vFilter = array();
        CopyRecordDateValue($vFilter, $vValues, 'beg_date');
        CopyRecordDateValue($vFilter, $vValues, 'end_date');
//        CopyParam($vFilter, $vValues, 'Order');
        Redirect( CompoundURL('plasters_list.html', $vFilter) );
    }
    else
    {
        $vTemplate =& CreateTemplate();
        $vRenderer =& CreateRenderer($vTemplate);
        $vForm->accept($vRenderer);
        $vView =& new TData;
        $vView->form = $vRenderer->toObject();
        $vTemplate->compile('reg/plasters_list.html');
        $vTemplate->outputObject($vView);
    }

?>
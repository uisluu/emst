<?php

  #####################################################################
  #
  # Травмпункт. (c) 2005 Vista
  #
  #####################################################################
    require_once('config/config.php');

    class TEditor extends HTML_QuickFormEx
    {
        function TEditor($AId='')
        {
            $vDB = GetDB();

            if ( !empty($AId) )
            {
                $vRecord = $vDB->GetById('emst_cases', $AId);
                if (  !is_array($vRecord) )
                   $vRecord = array();
                else
                {
                }
            }
            else
            {
                $vRecord = array();
                $vRecord['accident_datetime'] = date('Y-m-d');
            }


            $this->HTML_QuickForm('frmItem', 'post', $_SERVER['REQUEST_URI']);
            $this->setMyRequiredNote();
            $this->addElement('header',   'header1',         'Паспортные данные');
            $this->addElement('hidden',   'id',              '');
            $this->addElement('text',     'last_name',       'Фамилия',          array('class'=>'edt_100'));
            $this->addElement('text',     'first_name',      'Имя',              array('class'=>'edt_100'));
            $this->addElement('text',     'patr_name',       'Отчество',         array('class'=>'edt_100'));

            $this->addElement('submit',   'Submit',              'Ok');
            $this->applyFilter('_ALL_',   'trim');

            $this->addRule('last_name',  'Это поле обязательно для заполнения', 'required');
            $this->addRule('first_name', 'Это поле обязательно для заполнения', 'required');
//          $this->addRule('address',    'Это поле обязательно для заполнения', 'required');

            $this->setDefaults($vRecord);
        }

        function Save()
        {
            if ( !$this->validate() )
                return FALSE;

            $vDB = GetDB();
            $vValues = $this->getSubmitValues(TRUE);
            $vId = @$vValues['id'];
            $vRecord = array();

            if ( !empty($vId) )
            {
              $vRecord['id'] = $vId;
            }
            else
            {
              $vRecord['create_time'] = $vDB->ConvertToDateTime(time());
              $vRecord['next_visit_date'] = $vDB->ConvertToDate(time());
            }

            $vRecord['modify_time'] = $vDB->ConvertToDateTime(time());
            CopyRecordStrValue( $vRecord, $vValues, 'last_name');
            CopyRecordStrValue( $vRecord, $vValues, 'first_name');
            CopyRecordStrValue( $vRecord, $vValues, 'patr_name');

          /* Здесь нужна проверка введенных данных в поле ПОЛИС СМО*/
            $vResult = $vDB->InsertOrUpdateById('emst_cases', $vRecord);

//       var_dump($vRecord);
//       var_dump($vDB);

           return $vResult;
        }

    }


    $vId = @$_GET['id'];
    $vForm = new TEditor($vId);
    if ( !$vForm->Save() )
    {
        $vTemplate =& CreateTemplate();
        $vRenderer =& CreateRenderer($vTemplate);
        $vForm->accept($vRenderer);
        $vView =& new TBaseView();
        $vView->form = $vRenderer->toObject();
        $vTemplate->compile('reg/case_edit.html');
        $vTemplate->outputObject($vView);
    }
    else
    {
        RedirectToList('cases.html');
    }

?>

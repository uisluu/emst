<?php

    require_once 'library/users.php';

    class TEditor extends HTML_QuickFormEx
    {
        function TEditor($AId='')
        {
            $vDB = GetDB();
            if ( !empty($AId) )
            {
                $vRecord = $vDB->GetById('users', $AId);
                if (  !is_array($vRecord) )
                   $vRecord = array();
                else
                {
                    $vRecord['password2'] = $vRecord['password'];
                }
            }
            else
                $vRecord = array();


            $this->HTML_QuickForm('frmItem', 'post', $_SERVER['REQUEST_URI']);
            $this->setMyRequiredNote();
            $this->addElement('header',   'header',          'Описание пользователя');
            $this->addElement('hidden',   'id',              '');
            $this->addElement('text',     'login',           'Login',          array('class'=>'edt_avg'));
            $this->addElement('text',     'full_name',       'ФИО',            array('class'=>'edt_avg'));
            $this->addElement('password', 'password',        'Пароль',         array('class'=>'edt_avg'));
            $this->addElement('password', 'password2',       'Пароль ещё раз', array('class'=>'edt_avg'));
            $this->addElement('text',     'eisCode',         'Код ЕИС ОМС',    array('class'=>'edt_avg'));
            $vRolesList = GetRolesList();
            $this->addElement('select',   'roles',           'Роли',
                        $vRolesList,
                        array('multiple' => 'multiple', 'size' => count($vRolesList), 'class'=>'edt_avg')
                       );

            $this->addElement('checkbox', 'retired',         'Доступ запрещён');

//            $this->addElement('textarea', 'Notes',           'Notes', array('rows' => 6, 'cols' => 70));

            $this->addElement('submit',   'Submit',     'Ok');
            $this->applyFilter('_ALL_', 'trim');

            $this->addRule('login', 'Это поле обязательно для заполнения', 'required');
            $this->addRule(array('password', 'password2'), 'пароли должны совпадать', 'compare');
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
            CopyRecordStrValue($vRecord, $vValues, 'login');
            CopyRecordStrValue($vRecord, $vValues, 'full_name');
            CopyRecordStrValue($vRecord, $vValues, 'password');
            CopyRecordStrValue($vRecord, $vValues, 'eisCode');
            CopyRecordSelValue($vRecord, $vValues, 'roles');
            CopyRecordBoolValue($vRecord, $vValues, 'retired');
            $vResult = $vDB->InsertOrUpdateById('users', $vRecord);

//       var_dump($vRecord);
//       var_dump($vDB);

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
        $vTemplate->compile('refs/user_edit.html');
        $vTemplate->outputObject($vView);
    }
    else
    {
        RedirectToList('users.html');
    }

?>
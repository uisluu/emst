<?php

  #####################################################################
  #
  # Травмпункт. (c) 2005 Vista
  #
  #####################################################################

require_once 'library/fpdfex.php';
require_once 'library/cases_table.php';


    function ConstructQuery(&$ADB, $AParams)
    {
        $vFilter = array();

        $vTable = ' emst_surgeries';

        if ( array_key_exists('beg_date', $AParams) )
           $vFilter[] = $ADB->CondGE('date', $AParams['beg_date']);
        if ( array_key_exists('end_date', $AParams) )
           $vFilter[] = $ADB->CondLT('date', DateAddDay($AParams['end_date']));
        //if ( array_key_exists('paytype', $AParams) )
        //   $vFilter[] = $ADB->CondEqual('paytype', $AParams['paytype']);

        $vFilter = implode(' AND ', $vFilter);
//        $vFilter = '(case_id = 11619) or (case_id = 11591)';
        $vOrder = 'case_id, date, id';
        return array($vTable, $vFilter, $vOrder);
    }


    function& locGetSMOList()
    {
        static $vList;

        if ( empty($vList) )
        {
            $vDB = GetDB();
            $vList = $vDB->GetRBList('rb_insurance_companies', 'id', 'name', false);
//            Trace($vList);
        }
        return $vList;
    }


    function getUserEisCode($AUserId)
    {
	$vDB = GetDB();
        $vRecord = $vDB->GetById('users', $AUserId, 'eisCode');
	$vResult = $vRecord ? $vRecord['eisCode'] : NULL;
	$vResult = $vResult ? $vResult : '0';
#	print_r( array( $AUserId, $vRecord, $vResult) );
	return $vResult;
    }


    function Date2DBF($AMySQLDate)
    {
        @list($vDate, $vTime) = explode(' ', $AMySQLDate, 2);
        @list($vYear, $vMonth, $vDay) = explode('-',$vDate,3);
        $vResult = str_pad($vYear,  4, '0', STR_PAD_LEFT) .
                   str_pad($vMonth, 2, '0', STR_PAD_LEFT) .
                   str_pad($vDay,   2, '0', STR_PAD_LEFT);
        return $vResult;
    }


    function OutSurgery($ADBF, $ACase, $ASurgery, $AService)
    {
        $vSMOList   = locGetSMOList();
        $vDocSeries = trim(str_replace('-', ' ', $ACase['doc_series']));
        $vSpacePos = strpos($vDocSeries, ' ');
        if ( $vSpacePos === false )
        {
            $vSerLeft  = substr($vDocSeries, 0, 2);
            $vSerRight = substr($vDocSeries, 2);
        }
        else
        {
            $vSerLeft  = substr($vDocSeries, 0, $vSpacePos);
            $vSerRight = substr($vDocSeries, $vSpacePos+1);
        }

        $vBornDate = $ACase['born_date'];
        if ( empty($vBornDate) )
            $vBornDate = '0000-00-00';
        $vBornDate = explode('-', $vBornDate);

        if ( $vBornDate[0] == '0000' )
            $vBornDate[0] = '1901';
        if ( $vBornDate[1] == '00' )
            $vBornDate[1] = '01';
        if ( $vBornDate[2] == '00' )
            $vBornDate[2] = '01';
        $vBornDate = implode('-', $vBornDate);
        $vSurgeryDate = $ASurgery['date'];

//        Trace(@$ACase['insurance_company_id'].'=->'.@$vSMOList[$ACase['insurance_company_id']]);

        $vFirstDoctorId = $ACase['first_doctor_id'];
	$CurrDoctorId = $ASurgery['user_id'];


        $vRecord = array(
          FormatName($ACase['last_name']),          // array('SURNAME',    'C', 18), //   Фамилия пациента
          FormatName($ACase['first_name']),         // array('NAME1',      'C', 15), //   Имя пациента
          FormatName($ACase['patr_name']),          // array('NAME2',      'C', 15), //   Отчество пациента
          Date2DBF($vBornDate),                     // array('BIRTHDAY',   'D'),     //   Дата рождения
          $ACase['is_male']?'м':'ж',    // array('SEX',        'C',  1), //   Пол
          'э',                          // array('ORDER',      'C',  1), //   Признак экстренности случая лечения (если случай экстренный - принимает значение 'э' или 'Э')
          strtoupper($ACase['polis_series']),       // array('POLIS_S',    'C', 10), //   Серия полиса
          strtoupper($ACase['polis_number']),       // array('POLIS_N',    'C', 20), //   Номер полиса
          '', // @$vSMOList[$ACase['insurance_company_id']], // array('POLIS_W',    'C',  5), //   Код СМО, выдавшей полис
          '', // @$vSMOList[$ACase['insurance_company_id']], // array('PAYER',      'C',  5), //   Код СМО, выдавшей полис?
//          $ACase['addr_reg_street'],    //  array('STREET',     'C',  5), //   Адрес пациента: код улицы
          $ACase['addr_reg_street'],    //  array('STREET',     'C',  45), //   Адрес пациента: код улицы
          '',                           // array('STREETYPE',  'C',  2), //   Адрес пациента: тип улицы
          '',                           // array('AREA',       'C',  3), //   Адрес пациента: код район
          $ACase['addr_reg_num'],       // array('HOUSE',      'C',  7), //   Адрес пациента: номер дома
          $ACase['addr_reg_subnum'],    // array('KORP',       'C',  2), //   Адрес пациента: корпус
          $ACase['addr_reg_apartment'], // array('FLAT',       'C', 15), //   Адрес пациента: номер квартиры
          'аТрОт',                     // array('PROFILE',    'C',  6), //   Код профиля лечения
          CalcAge($vBornDate,$vSurgeryDate) >= 18 ? 'в':'д', // array('PROFILENET', 'C',  1), //   Тип сети профиля (в - взрослая, д - детская)
          Date2DBF($vSurgeryDate),      // array('DATEIN'      'D'),     //   Дата начала услуги
          Date2DBF($vSurgeryDate),      // array('DATEOUT'     'D'),     //   Дата окончания услуги
          '1',                          //  array('AMOUNT'      'N', 15, 5), //  Объем лечения
          str_replace(' ', '', $ACase['diagnosis_mkb']),    // array('DIAGNOSIS',  'C',  5), //   Код диагноза
          false,                        // array('SEND'        'L'),     //   Флаг обработки записи
          '',                           // array('ERROR',      'C', 15), //   Описание ошибки
          $ACase['doc_type_id'],        // array('TYPEDOC',    'C',  1), //   Тип документа
          $vSerLeft,                    // array('SER1',       'C', 10), //   Серия документа, левая часть
          $vSerRight,                   // array('SER2',       'C', 10), //   Серия документа, левая часть
          $ACase['doc_number'],         // array('NPASP',      'C', 10)  //   Номер документа
//          @$ACase['addr_reg_street'],
          FormatAddress(@$ACase['addr_reg_street'],
                        @$ACase['addr_reg_num'],
                        @$ACase['addr_reg_subnum'],
                        @$ACase['addr_reg_apartment']),  // array('LONGADDR',   'C', 128) //   Длиный адрес
          @$ACase['id'],
	  1,                            // array('CASE_CAST',  'N', 2, 0) //   
          0,				// array('AMOUNT_D',   'N', 3, 0) //   

          23,                                            // array('ID_EXITUS',  'N', 2),  // исход лечения
          'тр.'.@$ACase['id'],                           // array('ILLHISTORY', 'C', 20), // история болезни (id клиента)
          118, // array('ID_PRMP',    'N', 3,0),         // Код профиля по Классификатору профиля
          118, // array('ID_PRMP_C',  'N', 3,0),         // Код профиля по Классификатору профиля для случая лечения
          str_replace(' ', '', $ACase['diagnosis_mkb']), // array('DIAG_C',     'C', 5),   // Код диагноза для случая лечения
          '',                                            // array('DIAG_S_C',   'C', 5),   // Код сопутствующего диагноза для случая лечения
          '',                                            // array('DIAG_P_C',   'C', 5),   // Код первичного диагноза для случая лечения
          16,                                            // array('QRESULT',    'N', 3,0), // Результат обращения за медицинской помощью
          31,                                            // array('ID_PRVS',    'N', 10,0),// ID врачебной специальности
          31,                                            // array('ID_PRVS_C',  'N', 10,0),// ID врачебной специальности для случая лечения
          29,                                             // array('ID_SP_PAY',  'N', 2,0), // ID способа оплаты медицинской помощи
          1,                                             // array('ID_ED_PAY',  'N', 5,2), // Количество единиц оплаты медицинской помощи
          1,                                             // array('ID_VMP',     'N', 2,0)  // ID вида медицинской помощи
          getUserEisCode($CurrDoctorId),                 // array('ID_DOC',     'C', 20),  // Идентификатор врача из справочника SPRAV_DOC.DBF (для услуги)
	  getUserEisCode($vFirstDoctorId),               // array('ID_DOC_C',   'C', 20),  // Идентификатор врача из справочника SPRAV_DOC.DBF (для случая)
	  '1',					         // array('ID_DEPT',    'C', 20),  // Идентификатор отделения МО из справочника SPRAV_DEPTS.DBF (для услуги)
	  '1',                                           // array('ID_DEPT_C',  'C', 20),  // Идентификатор отделения МО из справочника SPRAV_DEPTS.DBF (для случая)
          0,                                             // array('ID_LPU_D',   'N', 20,0),// Идентификатор ЛПУ, направившего на лечение (из справочника SPRAV_LPU.DBF)
	  0,
	  0,
	  0,
	  3,
	  5,
	  5,
      0,
      2,
      0,
      0,
      0,
      0
        );

        $vOutRecord = array();
        foreach( $vRecord as $vField )
          $vOutRecord[] = iconv('UTF-8','CP866',$vField);
        dbase_add_record($ADBF, $vOutRecord);
    }

// =======================================================================

    if ( !array_key_exists('beg_date', $_GET) )
      $_GET['beg_date'] = date('Y-m-d');
    if ( !array_key_exists('end_date', $_GET) )
      $_GET['end_date'] = date('Y-m-d');
    if ( array_key_exists('service', $_GET) )
    	$vService = $_GET['service'];
        else
    	$vService = defautService;
    if ( !array_key_exists('paytype', $_GET) )
      $_GET['paytype'] = 0;
    
      
// creation
    $DBFName = tempnam('/tmp', 'stats');
    rename( $DBFName, $DBFName.'.dbf' );
    $DBFName = $DBFName.'.dbf';

    $DBFDef  = array(
        array('SURNAME',    'C', 18), //   Фамилия пациента
        array('NAME1',      'C', 15), //   Имя пациента
        array('NAME2',      'C', 15), //   Отчество пациента
        array('BIRTHDAY',   'D'),     //   Дата рождения
        array('SEX',        'C',  1), //   Пол
        array('ORDER',      'C',  1), //   Признак экстренности случая лечения (если случай экстренный - принимает значение 'э' или 'Э')
        array('POLIS_S',    'C', 10), //   Серия полиса
        array('POLIS_N',    'C', 20), //   Номер полиса
        array('POLIS_W',    'C',  5), //   Код СМО, выдавшей полис
        array('PAYER',      'C',  5), //   Код СМО, выдавшей полис?
//        array('STREET',     'C',  5), //   Адрес пациента: код улицы
        array('STREET',     'C',  45), //   Адрес пациента: код улицы
        array('STREETYPE',  'C',  2), //   Адрес пациента: тип улицы
        array('AREA',       'C',  3), //   Адрес пациента: код район
        array('HOUSE',      'C',  7), //   Адрес пациента: номер дома
        array('KORP',       'C',  2), //   Адрес пациента: корпус
        array('FLAT',       'C', 15), //   Адрес пациента: номер квартиры
        array('PROFILE',    'C',  6), //   Код профиля лечения
        array('PROFILENET', 'C',  1), //   Тип сети профиля (в - взрослая, д - детская)
        array('DATEIN',     'D'),     //   Дата начала услуги
        array('DATEOUT',    'D'),     //   Дата окончания услуги
        array('AMOUNT',     'N', 15, 5), //  Объем лечения
        array('DIAGNOSIS',  'C',  5), //   Код диагноза
        array('SEND',       'L'),     //   Флаг обработки записи
//        array('ERROR',      'C', 15), //   Описание ошибки
        array('ERROR',      'C', 25), //   Описание ошибки
        array('TYPEDOC',    'C',  1), //   Тип документа
        array('SER1',       'C', 10), //   Серия документа, левая часть
        array('SER2',       'C', 10), //   Серия документа, левая часть
        array('NPASP',      'C', 10), //   Номер документа
        array('LONGADDR',   'C', 120), //   Длиный адрес
        array('MYCASEID',   'C', 8),   //   CaseID
        array('CASE_CAST',  'N', 2, 0),//   
        array('AMOUNT_D',   'N', 3, 0), //   
// new	
        array('ID_EXITUS',  'N', 2,0), // исход лечения
        array('ILLHISTORY', 'C', 20), // история болезни (id клиента)
        array('ID_PRMP',    'N', 3,0), // Код профиля по Классификатору профиля
        array('ID_PRMP_C',  'N', 3,0), // Код профиля по Классификатору профиля для случая лечения
        array('DIAG_C',     'C', 5),   // Код диагноза для случая лечения
        array('DIAG_S_C',   'C', 5),   // Код сопутствующего диагноза для случая лечения
        array('DIAG_P_C',   'C', 5),   // Код первичного диагноза для случая лечения
        array('QRESULT',    'N', 3,0), // Результат обращения за медицинской помощью
        array('ID_PRVS',    'N', 10,0),// ID врачебной специальности
        array('ID_PRVS_C',  'N', 10,0),// ID врачебной специальности для случая лечения
        array('ID_SP_PAY',  'N', 2,0), // ID способа оплаты медицинской помощи
        array('ID_ED_PAY',  'N', 5,2), // Количество единиц оплаты медицинской помощи
        array('ID_VMP',     'N', 2,0), // ID вида медицинской помощи
	
        array('ID_DOC',     'C', 20),  // Идентификатор врача из справочника SPRAV_DOC.DBF (для услуги)
	array('ID_DOC_C',   'C', 20),  // Идентификатор врача из справочника SPRAV_DOC.DBF (для случая)
	array('ID_DEPT',    'C', 20),  // Идентификатор отделения МО из справочника SPRAV_DEPTS.DBF (для услуги)
	array('ID_DEPT_C',  'C', 20),  // Идентификатор отделения МО из справочника SPRAV_DEPTS.DBF (для случая)
        array('ID_LPU_D',   'N', 20,0),// Идентификатор ЛПУ, направившего на лечение (из справочника SPRAV_LPU.DBF)
	array('IDSERVDATA', 'N', 10,0),
	array('IDSERVMADE', 'N', 1,0),
	array('IDSERVLPU',  'N', 10,0),
	array('ID_GOAL',    'N', 5,0),
	array('ID_GOAL_C',  'N', 5,0),
	array('ID_GOSP',    'N', 5,0),

    array('IDVIDVME',   'N', 2,0), // Идентификатор вида мед. вмешательства
    array('IDFORPOM',   'N', 2,0), // Идентификатор формы оказания помощи
    array('IDVIDHMP',   'N', 2,0), // Идентификатор вида высокотехнологичной мед помощи
    array('IDMETHMP',   'N', 2,0), // Идентификатор вида высокотехнологичной мед помощи
    array('ID_PRVS_D',  'N', 10,0), // Идентификатор специальности направившого врача
    array('ID_GOAL_D',  'N', 5,0) // ID цели обращения при направлении
    );

    if ( !($vDBF = dbase_create($DBFName, $DBFDef)) )
    {
        print "<strong>can't create $DBFName!</strong>";
        exit;
    }

    $vDB = GetDB();
    list($vTable, $vFilter, $vOrder) = ConstructQuery($vDB, $_GET);
    $vCaseID    = 'not an id';
    $vCase      = array();
    $vRecords   = $vDB->Select($vTable, '*', $vFilter, $vOrder);

    while( $vRecord = $vRecords->Fetch() )
    {
        $vRecCaseID = $vRecord['case_id'];
        if ( $vCaseID !== $vRecCaseID )
        {
            $vCaseID = $vRecCaseID;
            $vCase   = $vDB->GetById('emst_cases', $vCaseID);
        }
	      if ( $vCase['paytype'] == $_GET['paytype'] )
            OutSurgery($vDBF, $vCase, $vRecord, $vService);
    }

    dbase_close($vDBF);

    $vHandle = fopen($DBFName, 'rb');
    header('Content-type: application/octet-stream');
    header('Content-Disposition: inline; filename=stats.dbf');
    header('Content-length: '.filesize($DBFName));
    header('Expires: '.gmdate('D, d M Y H:i:s', mktime(date('H')+2, date('i'), date('s'), date('m'), date('d'), date('Y'))).' GMT');
    header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');

    while( !feof($vHandle) && (connection_status()==0))
    {
        print(fread($vHandle, 1024*8));
        flush();
    }

    fclose($vHandle);
    unlink($DBFName);
?>

<?php

#####################################################################
#
# Травмпункт. (c) 2005 Vista
#
#####################################################################

require_once 'library/fpdfex.php';
require_once 'library/cases_table.php';


class TPDF_Report extends FPDFEx
{
    function TPDF_Report()
    {
        $this->FPDFEx('Stats', 'L');
        $this->Open();
        $this->SetMargins(10,10,10);
    }


    function Render($AParams)
    {
        $vWidths = array(50,  20,  20,  20,  20,  20,  20,  20,  20,   20, 20, 20);
        $vAligns = array('L', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R');

        $vDB = GetDB();
        $vBegDate = $AParams['beg_date'];
        $vEndDate = $AParams['end_date'];
        $vPayType = $AParams['paytype'];

        $this->SetFont('arial_rus','',10);
        $vHeight = $this->FontSize*1.5;
        $vWidth  = $this->GetAreaWidth();
        $this->AddPage();

        $this->Cell($vWidth, $vHeight, iconv('utf-8', 'cp1251','Отчет по явкам'), '', 0, 'C');
        $this->Ln($vHeight);
        $this->Cell($vWidth, $vHeight, iconv('utf-8', 'cp1251','за период с ').
                                       iconv('utf-8', 'cp1251',Date2ReadableLong($vBegDate)).
                                       iconv('utf-8', 'cp1251',' г. по ').
                                       iconv('utf-8', 'cp1251',Date2ReadableLong($vEndDate)).
                                       iconv('utf-8', 'cp1251',' г.'), '', 0, 'C');
        $this->Ln($vHeight*2);

        $vTable  = 'emst_surgeries'.
                   '  LEFT JOIN emst_cases ON emst_surgeries.case_id=emst_cases.id'.
                   '  LEFT JOIN users      ON emst_surgeries.user_id=users.id'.
                   '  LEFT JOIN rb_employment_categories ON emst_cases.employment_category_id = rb_employment_categories.id'.
                   '  LEFT JOIN rb_clinical_outcomes     ON emst_surgeries.clinical_outcome_id = rb_clinical_outcomes.id';

        $vFields = 'emst_surgeries.user_id as user_id,'.
                   'users.full_name as full_name,'.
                   '(DATE(emst_surgeries.date)=DATE(emst_cases.create_time)) as is_primary,'.
                   '(emst_cases.doc_series="" OR emst_cases.doc_number="" OR emst_cases.polis_series="" OR emst_cases.polis_number="") as is_bad_doc,'.
                   gSurgeryWithBadIllDoc.' as is_bad_illdoc,'.
                   gLostOutcome.' as is_lost_outcome,'.
                   'emst_surgeries.eisoms_status as eisoms_status,'.
                   'count(emst_surgeries.id) as surgeries_count';

        $vFilter = $vDB->CondGE('date', $vBegDate) .
                   ' AND ' .
                   $vDB->CondLT('date', DateAddDay($vEndDate)) .
                   ' AND ' .
                   $vDB->CondEqual('paytype', $vPayType) .
                   '  GROUP BY emst_surgeries.user_id, is_primary, is_bad_doc, eisoms_status, is_bad_illdoc, is_lost_outcome';

        $vOrder  = 'users.full_name';

        $vReport = array();
        $vRecords= $vDB->Select($vTable, $vFields, $vFilter, $vOrder);
        while( $vRecord = $vRecords->Fetch() )
        {
            $vUserID = '_'.@($vRecord['user_id']);
            if ( empty($vReport[$vUserID]) )
            {
                $vReport[$vUserID] = array('name'=>$vRecord['full_name']);
            }
            $vReportLine = &$vReport[$vUserID];
            $vColName    = 'Col'.$vRecord['is_primary'].$vRecord['is_bad_doc'];
            $vReportLine[$vColName] = (@$vReportLine[$vColName])+$vRecord['surgeries_count'];

            $vColName    = 'IllDoc'.$vRecord['is_bad_illdoc'];
            $vReportLine[$vColName] = (@$vReportLine[$vColName])+$vRecord['surgeries_count'];

            $vColName    = 'IsLostOutcome'.$vRecord['is_lost_outcome'];
            $vReportLine[$vColName] = (@$vReportLine[$vColName])+$vRecord['surgeries_count'];

            $vColName    = 'EISOMSStatus'.$vRecord['eisoms_status'];
            $vReportLine[$vColName] = (@$vReportLine[$vColName])+$vRecord['surgeries_count'];
        }

        $vTotalCol00 = 0;
        $vTotalCol01 = 0;
        $vTotalCol10 = 0;
        $vTotalCol11 = 0;
        $vTotalIllDoc1 = 0;
        $vTotalIsLostOutcome1= 0;
        $vTotalEISOMSStatus0 = 0;
        $vTotalEISOMSStatus1 = 0;
        $vTotalEISOMSStatus2 = 0;

        $vRowData = array(iconv('utf-8', 'cp1251','Врач'),
                          iconv('utf-8', 'cp1251','Первичные'),
                          iconv('utf-8', 'cp1251','в т.ч. с ошибками в документах'),
                          iconv('utf-8', 'cp1251','Повторные'),
                          iconv('utf-8', 'cp1251','в т.ч. с ошибками в документах'),
                          iconv('utf-8', 'cp1251','всего'),
                          iconv('utf-8', 'cp1251','в т.ч. с ошибками в документах'),
                          iconv('utf-8', 'cp1251','с ошибками в б/л'),
                          iconv('utf-8', 'cp1251','Без указания исхода'),
                          iconv('utf-8', 'cp1251','ЕИС ОМС: не отпр.'),
                          iconv('utf-8', 'cp1251','ЕИС ОМС: ошибки'),
                          iconv('utf-8', 'cp1251','ЕИС ОМС: приняты'));
        $this->OutputTableRow($vWidths, $vHeight, $vRowData, $vAligns);

        foreach($vReport as $vUserID=>$vUserData)
        {
            $vName  = iconv('utf-8', 'cp1251',$vUserData['name']);
            $vCol00 = @$vUserData['Col00']; // повторные, нормальные
            $vCol01 = @$vUserData['Col01']; // повторные, док

            $vCol10 = @$vUserData['Col10']; // первичные, нормальные
            $vCol11 = @$vUserData['Col11']; // первичные, док

            $vEISOMSStatus0 = @$vUserData['EISOMSStatus0']; // не отправлялось в ЕИС ОМС
            $vEISOMSStatus1 = @$vUserData['EISOMSStatus1']; // не принято ЕИС ОМС (ошибки)
            $vEISOMSStatus2 = @$vUserData['EISOMSStatus2']; // приняты ЕИС ОМС

            $vIllDoc1 = @$vUserData['IllDoc1']; // б/л
            $vIsLostOutcome1 = @$vUserData['IsLostOutcome1']; // нет исхода

            $vRowData = array($vName,
                              ($vCol10+$vCol11),
                              ($vCol11),
                              ($vCol00+$vCol01),
                              ($vCol01),
                              ($vCol00+$vCol01+$vCol10+$vCol11),
                              ($vCol11+$vCol01),
                              $vIllDoc1,
                              $vIsLostOutcome1,
                              $vEISOMSStatus0,
                              $vEISOMSStatus1,
                              $vEISOMSStatus2);
            $this->OutputTableRow($vWidths, $vHeight, $vRowData, $vAligns);
            $vTotalCol00 += $vCol00;
            $vTotalCol01 += $vCol01;
            $vTotalCol10 += $vCol10;
            $vTotalCol11 += $vCol11;

            $vTotalEISOMSStatus0 += $vEISOMSStatus0;
            $vTotalEISOMSStatus1 += $vEISOMSStatus1;
            $vTotalEISOMSStatus2 += $vEISOMSStatus2;

            $vTotalIllDoc1 += $vIllDoc1;
            $vTotalIsLostOutcome1 += $vIsLostOutcome1;
        }

        $vRowData = array(iconv('utf-8', 'cp1251','Всего'),
                          ($vTotalCol10+$vTotalCol11),
                          ($vTotalCol11),
                          ($vTotalCol00+$vTotalCol01),
                          ($vTotalCol01),
                          ($vTotalCol00+$vTotalCol01+$vTotalCol10+$vTotalCol11),
                          ($vTotalCol11+$vTotalCol01),
                          $vTotalIllDoc1,
                          $vTotalIsLostOutcome1,
                          $vTotalEISOMSStatus0,
                          $vTotalEISOMSStatus1,
                          $vTotalEISOMSStatus2);
        $this->OutputTableRow($vWidths, $vHeight, $vRowData, $vAligns);
    }

}

// =======================================================================

if ( !array_key_exists('beg_date', $_GET) )
  $_GET['beg_date'] = date('Y-m-d');
if ( !array_key_exists('end_date', $_GET) )
  $_GET['end_date'] = date('Y-m-d');

$vDoc = new TPDF_Report;
$vDoc->Render($_GET);

header('Accept-Ranges: bytes');
$vDoc->Output('Surgeries_report.pdf', 'I');
?>

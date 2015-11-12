<?php

#####################################################################
#
# EMST:����������
# (c) 2005,2006 Vista
#
# ������ ����������� �� ���
#
#####################################################################


require_once './library/fpdfex.php';
require_once './library/cases_table.php';


class TPDF_Physiotherapy extends FPDFEx
{
    function TPDF_Physiotherapy()
    {
        $this->FPDFEx('RemedialGymnastics', 'L', 'A4');
        $this->SetAutoPageBreak(true, 10);
        $this->SetMargins(10,10,10);
        $this->Open();
    }


    function Render(&$AInfo)
    {
        $vDB = GetDB();

        $vBranchInfo = GetBranchInfo();

        $this->AddPage();

        $this->SetFont('arial_rus','',10);
        $vWidth  = $this->GetAreaWidth()/2-10;
        $vHeight   = $this->FontSize;
        $vHeightLN = $this->FontSize*1.5;

        $vX = $this->GetX();
        $vY = $this->GetY();

        $this->SetXY($vX, $vY);
        $this->Cell($vWidth, $vHeight, '������������ ���������������', '', 0, 'L');
        $this->Ln($vHeightLN);
        $this->Cell($vWidth, $vHeight, '� ����������� ��������', '', 0, 'L');
        $this->Ln($vHeightLN);
        $this->Cell($vWidth, $vHeight, '���������� ���������', '', 0, 'L');
        $this->Ln($vHeightLN);
        $this->Cell($vWidth, $vHeight, iconv("utf8","windows-1251",@$vBranchInfo['name']), '', 0, 'L');
        $this->Cell($vWidth, $vHeight, '', '', 0, 'L');
        $this->Ln($vHeightLN);
        $this->ExactCell($vHeight, '��� ����  ');
        $this->BoxedText($vWidth, $vHeight, iconv("utf8","windows-1251",$vBranchInfo['OGRN']));
        $this->Ln($vHeightLN);

        $this->SetFont('arial_rus','',10);
        $vHeight   = $this->FontSize;
        $vHeightLN = $this->FontSize*1.5;
        $this->Cell($vWidth, $vHeight, '����������� �� ���', '', 0, 'C');
        $this->Ln($vHeightLN);
//        $this->Notes('����� ������������� (���.) �������� �', 0, $vWidth, 1, @$AInfo['case_id']);
        $this->Notes('�������, ���, ��������', 0, $vWidth, 1, FormatShortNameEx($AInfo));
        $vX = $this->GetX();
        $vY = $this->GetY();
        $this->Notes('�������   ', 0, $vWidth/2, 1, CalcAge(@$AInfo['born_date']));
        $this->SetXY($vX+$vWidth/2, $vY);
        $this->Notes('���',      0, $vWidth/2, 1, iconv("utf8","windows-1251",FormatSex($AInfo['is_male'])));
        $vBlock = array();
        $vBlock[] = array( 'title'=>'�����',    'text'=>iconv("utf8","windows-1251",FormatAddress(@$AInfo['addr_reg_street'], @$AInfo['addr_reg_num'], @$AInfo['addr_reg_subnum'], @$AInfo['addr_reg_apartment'])));
        $vBlock[] = array( 'title'=>'��������', 'text'=>iconv("utf8","windows-1251",FormatDocument(@$AInfo['doc_type_id'], @$AInfo['doc_series'], @$AInfo['doc_number'])));
        $vBlock[] = array( 'title'=>'�����',    'text'=>iconv("utf8","windows-1251",FormatPolisEx(@$AInfo['insurance_company_id'], @$AInfo['polis_series'], @$AInfo['polis_number'])));
        $vBlock[] = array( 'title'=>'�������',  'text'=>iconv("utf8","windows-1251",@$AInfo['diagnosis']), 'rows'=>3);
        $vBlock[] = array( 'title'=>'����',     'text'=>iconv("utf8","windows-1251",Date2ReadableLong(date('Y-m-d', time()))));
        $vBlock[] = array( 'title'=>'����',     'text'=>iconv("utf8","windows-1251",FormatUserName(@$AInfo['doctor_id'])));
        $this->BlockNotes($vBlock, $vWidth);
    }
}


// Trace($_GET);
$vDoc = new TPDF_Physiotherapy;
$vDoc->Render($_GET);

//while (ob_get_level())
//    ob_end_clean();

header('Accept-Ranges: bytes');
$vDoc->Output('RemedialGymnastics.pdf', 'I');

?>

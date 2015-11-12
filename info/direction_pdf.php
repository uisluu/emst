<?php

#####################################################################
#
# EMST:����������
# (c) 2005,2006 Vista
#
# ������ �����������
#
#####################################################################


require_once './library/fpdfex.php';
require_once './library/cases_table.php';


class TPDF_Direction extends FPDFEx
{
    function TPDF_Direction()
    {
        $this->FPDFEx('Direction');
        $this->Open();
    }

    function Header()
    {
    }

    function Footer()
    {
    }



    function Render(&$ADirection)
    {
        $vDB = GetDB();
        $vSubjectList = $vDB->GetRBList('rb_directions','id', 'name', true);

        $this->SetMargins(20,20,20);
        $this->SetAutoPageBreak(true, 30);

        $vBranchInfo = GetBranchInfo();

        $this->AddPage();

        $this->SetFont('arial_rus','',10);
        $vWidth  = $this->GetAreaWidth();
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
        $this->Ln($vHeightLN);
        $this->ExactCell($vHeight, '��� ����  ');
        $this->BoxedText($vWidth, $vHeight, $vBranchInfo['OGRN']);
        $this->Ln($vHeightLN);

        $this->SetXY($vX, $vY);

        $this->Cell($vWidth, $vHeight, '����������� ������������' , '', 0, 'R');
        $this->Ln($vHeightLN);
        $this->Cell($vWidth, $vHeight, '����� � 057/�-04' , '', 0, 'R');
        $this->Ln($vHeightLN);
        $this->Cell($vWidth, $vHeight, '����������' , '', 0, 'R');
        $this->Ln($vHeightLN);
        $this->Cell($vWidth, $vHeight, '�������� ������������������� ������' , '', 0, 'R');
        $this->Ln($vHeightLN);
        $this->Cell($vWidth, $vHeight, '�� 22 ������ 2004 �. �255' , '', 0, 'R');
        $this->Ln($vHeightLN);
        $this->Ln($vHeightLN);

        $this->SetFont('arial_rus','',14);
        $vHeight   = $this->FontSize;
        $vHeightLN = $this->FontSize*1.5;
        $this->Cell($vWidth, $vHeight, '�����������', '', 0, 'C');
        $this->Ln($vHeightLN);
        $this->Cell($vWidth, $vHeight, iconv("utf8","windows-1251",@$vSubjectList[$ADirection['direction_subject']]), '', 0, 'C');
        $this->Ln($vHeightLN);
        $this->Cell($vWidth, $vHeight, iconv("utf8","windows-1251",@$ADirection['direction_target']), 'B', 0, 'C');
        $this->Ln($vHeightLN);
        $this->Ln($vHeightLN);

        $this->SetFont('arial_rus','',10);
        $vHeight   = $this->FontSize;
        $vHeightLN = $this->FontSize*1.5;
        $this->ExactCell($vHeight, '1. ����� ���������� ������ ���');
        $this->SetX(90);
//            $this->Ln($vHeight);
        $this->BoxedText($vWidth, $vHeight, iconv("utf8","windows-1251",@$ADirection['polis_series']).' '.@$ADirection['polis_number'], 24);
        $this->Ln($vHeightLN);
        $this->ExactCell($vHeight, '2. ��� ������');
        $this->SetX(90);
        $this->BoxedText($vWidth, $vHeight, '', 3);
        $this->Ln($vHeightLN);

        $vBlock = array();
        $vBlock[] = array( 'title'=>'3. �������, ���, ��������',
//                           'text' => FormatName(@$ADirection['last_name'], @$ADirection['first_name'], @$ADirection['patr_name']) );
                           'text' => iconv("utf8","windows-1251",FormatNameEx($ADirection) ));
        $vBlock[] = array( 'title'=>'4. ���� ��������',          'text'=>iconv("utf8","windows-1251",Date2ReadableLong(@$ADirection['born_date'])) );
        $vBlock[] = array( 'title'=>'5. ����� ����������� ����� ����������',
                           'text' =>iconv("utf8","windows-1251",@FormatAddress($ADirection['addr_reg_street']), iconv("utf8","windows-1251",$ADirection['addr_reg_num']), iconv("utf8","windows-1251",$ADirection['addr_reg_subnum']), iconv("utf8","windows-1251",$ADirection['addr_reg_apartment'])));
        $vBlock[] = array( 'title'=>'6. ����� ������, ���������',
                           'text'=>FormatProfession(iconv("utf8","windows-1251",@$ADirection['employment_place']), iconv("utf8","windows-1251",@$ADirection['profession'])));
        $vBlock[] = array( 'title'=>'7. �������',                'text'=>iconv("utf8","windows-1251",@$ADirection['diagnosis']));
        $this->BlockNotes($vBlock, $vWidth);
        $this->Ln($vHeightLN);
        $this->ExactCell($vHeight, '8. ��� �������� �� ���');
        $this->SetX(90);
        $this->BoxedText($vWidth, $vHeight, iconv("utf8","windows-1251",@$ADirection['diagnosis_mkb']), 5);
        $this->Ln($vHeightLN);

        $vBlock = array();
        $this->Ln($vHeightLN);
        $this->Ln($vHeightLN);
        $vBlock[] = array( 'title'=>'����',                   'text'=>iconv("utf8","windows-1251",FormatUserName(@$ADirection['doctor_id'])));
        $vBlock[] = array( 'title'=>'����',                   'text'=>iconv("utf8","windows-1251",Date2ReadableLong($vDB->ConvertToDate(time()))));
        $this->BlockNotes($vBlock, 70);
        $this->Ln($vHeightLN*2);
        $this->ExactCell($vHeight, ' �.�.');

    }
}


// Trace($_GET);
$vDoc = new TPDF_Direction;
$vDoc->Render($_GET);

//while (ob_get_level())
//    ob_end_clean();

header('Accept-Ranges: bytes');
$vDoc->Output('Direction.pdf', 'I');

?>

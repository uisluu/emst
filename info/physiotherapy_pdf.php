<?php

#####################################################################
#
# EMST:����������
# (c) 2005,2006 Vista
#
# ������ ����������� �� ������������
#
#####################################################################


require_once 'library/fpdfex.php';
require_once 'library/cases_table.php';

    function _2w(&$Src)
    {
        return iconv( "utf-8", "windows-1251", $Src );
    }

    function _2u(&$Src)
    {
        return iconv( "windows-1251", "utf-8", $Src );
    }

class TPDF_Physiotherapy extends FPDFEx
{
    function TPDF_Physiotherapy()
    {
        $this->FPDFEx('Physiotherapy', 'L', 'A4');
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
//        $this->Cell($vWidth, $vHeight, @$vBranchInfo['name'], '', 0, 'L');
        $this->Cell($vWidth, $vHeight, '', '', 0, 'L');
        $this->Ln($vHeightLN);
        $this->ExactCell($vHeight, '��� ����  ');
//        $this->BoxedText($vWidth, $vHeight, $vBranchInfo['OGRN']);
        $this->BoxedText($vWidth, $vHeight, '             ');
        $this->Ln($vHeightLN);

        $this->SetXY($vX, $vY);

        $this->Cell($vWidth, $vHeight, '����������� ������������' , '', 0, 'R');
        $this->Ln($vHeightLN);
        $this->Cell($vWidth, $vHeight, '����� � 044/�' , '', 0, 'R');
        $this->Ln($vHeightLN);
        $this->Cell($vWidth, $vHeight, '����������' , '', 0, 'R');
        $this->Ln($vHeightLN);
        $this->Cell($vWidth, $vHeight, '���������� ����' , '', 0, 'R');
        $this->Ln($vHeightLN);
        $this->Cell($vWidth, $vHeight, '04.10.80 �1030' , '', 0, 'R');
        $this->Ln($vHeightLN);
        $this->Ln($vHeightLN);

        $this->SetFont('arial_rus','',10);
        $vHeight   = $this->FontSize;
        $vHeightLN = $this->FontSize*1.5;
        $this->Cell($vWidth, $vHeight, '�����', '', 0, 'C');
        $this->Ln($vHeightLN);
        $this->Cell($vWidth, $vHeight, '��������, ���������� � �������������������� ��������� (��������)', '', 0, 'C');
        $this->Ln($vHeightLN);

        $this->Notes('����� ������������� (���.) �������� �', 0, $vWidth, 1, @$AInfo['case_id']);
        $this->Notes('������� ����', 0, $vWidth, 1, _2w(FormatUserName(@$AInfo['doctor_id'])));
//        $this->Notes('�������, ���, ��������', 0, $vWidth, 1, FormatShortName(@$AInfo['last_name'], @$AInfo['first_name'], @$AInfo['patr_name']));
/*
        $this->Notes('�������, ���, ��������', 0, $vWidth, 1, _2w(FormatShortNameEx($AInfo)));
        $vX = $this->GetX();
        $vY = $this->GetY();
        $this->Notes('�������', 0, $vWidth/2, 1, CalcAge(@$AInfo['born_date']));
        $this->SetXY($vX+$vWidth/2, $vY);
        $this->Notes('���', 0, $vWidth/2, 1, FormatSex($AInfo['is_male']));
*/
        $this->Notes('�������, ���, ��������', 0, $vWidth, 1, FormatShortNameEx($AInfo));
        $vX = $this->GetX();
        $vY = $this->GetY();
        $this->Notes('�������', 0, $vWidth/2, 1, CalcAge(@$AInfo['born_date']));
        $this->SetXY($vX+$vWidth/2, $vY);
        $this->Notes('���', 0, $vWidth/2, 1, _2w(FormatSex($AInfo['is_male'])));
        $this->Notes('�����', 0, $vWidth, 1, _2w(FormatAddress(@$AInfo['addr_reg_street'], @$AInfo['addr_reg_num'], @$AInfo['addr_reg_subnum'], @$AInfo['addr_reg_apartment'])));

        $this->Notes('�� ������ ��������� (��������) ��������� �������', 0, $vWidth, 1, _2w($vBranchInfo['name']), false);

        $vX = $this->GetX();
        $vY = $this->GetY();
        $this->Notes('�������', 0, $vWidth, 4, _2w(@$AInfo['diagnosis']), false);
        $vX1 = $this->GetX();
        $vY1 = $this->GetY();
        $this->SetFont('arial_rus','',4);
        $this->SetXY($vX, $vY+$vHeightLN*2-$this->FontSize/2);
        $this->Cell($vWidth, $vHeight, '����������� �����������, �� ������', '', 0, 'C');
        $this->Ln($vHeightLN);
        $this->Cell($vWidth, $vHeight, '�������� ������� ��������� �� ������������', '', 0, 'C');
        $this->SetXY($vX1, $vY1);

        $this->SetFont('arial_rus','',10);
        $this->Notes('������ ��������', 0, $vWidth, 2, '', false);

        $vX = $this->GetX();
        $vY = $this->GetY()+3;
        $this->SetXY($vX, $vY);

        $this->Cell(35, $vHeight, '���������� ');
        $this->Ln($vHeight);
        $this->Cell(35, $vHeight, '���������');
        $this->Ln($vHeight);
        $this->Cell(35, $vHeight, '������� ������');
        $this->Ln($vHeight);
        $this->Cell(35, $vHeight, '��� ������-');
        $this->Ln($vHeight);
        $this->Cell(35, $vHeight, '���������������');
        $this->Ln($vHeight);
        $this->Cell(35, $vHeight, '(�����������)');

        $vX += 38;
        $this->SetXY($vX, $vY);
        $this->Cell(15, $vHeight, '����', 'LTR', 0, 'C');
        $this->Cell(30, $vHeight, '������������', 'LTR', 0, 'C');
        $this->Cell(10, $vHeight, '�-��', 'LTR', 0, 'R');
        $this->Cell(20, $vHeight, '��������-', 'LTR', 0, 'C');
        $this->Cell(15, $vHeight, '����-', 'LTR', 0, 'C');
        $this->SetXY($vX, $vY+$vHeight);
        $this->Cell(15, $vHeight, '', 'LR', 0, 'C');
        $this->Cell(30, $vHeight, '���������', 'LR', 0, 'C');
        $this->Cell(10, $vHeight, '', 'LR', 0, 'C');
        $this->Cell(20, $vHeight, '���������', 'LR', 0, 'C');
        $this->Cell(15, $vHeight, '�����', 'LR', 0, 'C');
        for( $i=0; $i<3; $i++)
        {
            $this->SetXY($vX, $vY+$vHeight*2+$i*$vHeightLN);
            $this->Cell(15, $vHeightLN, '', 'LTRB', 0, 'C');
            $this->Cell(30, $vHeightLN, '', 'LTRB', 0, 'C');
            $this->Cell(10, $vHeightLN, '', 'LTRB', 0, 'C');
            $this->Cell(20, $vHeightLN, '', 'LTRB', 0, 'C');
            $this->Cell(15, $vHeightLN, '', 'LTRB', 0, 'C');
        }
       
        $this->Ln($vHeightLN);
        $vX = $this->GetX();
        $vY = $this->GetY();
        $this->Line($vX, $vY+$vHeight/2, $vX+$vWidth, $vY+$vHeight/2);
        $this->Ln($vHeight);

        $this->Cell($vWidth*0.6, $vHeight, '����� ���������� ���������: �������,');
        $this->Ln($vHeight);
        $this->Cell($vWidth*0.6, $vHeight, '������������, �� ���� (�����������)');
        $this->Ln($vHeight);

        $this->Cell($vWidth*0.6, $vHeight, '���� �������, ����������� ������');
        $this->Ln($vHeight);
        $this->Cell($vWidth*0.6, $vHeight, '������������ (� ��� ����� � ���������������)');
        $this->Ln($vHeight);
        $this->Notes('', 0, $vWidth*0.6, 3, '', false);

        $this->Notes('�������', 0, $vWidth*0.6, 2, '', false);
        $this->Notes('����-�������������', 0, $vWidth*0.6, 1, '');
        $this->Image('images/angels.jpeg', $vX+$vWidth*0.65, $vY+$vHeight, $vWidth*0.35);
    }
}


// Trace($_GET);
$vDoc = new TPDF_Physiotherapy;
$vDoc->Render($_GET);

//while (ob_get_level())
//    ob_end_clean();

header('Accept-Ranges: bytes');
$vDoc->Output('Physiotherapy.pdf', 'I');

?>

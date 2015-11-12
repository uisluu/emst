<?php

#####################################################################
#
# EMST:����������
# (c) 2005,2006 Vista
#
# ������ ������������ �������
#
#####################################################################


require_once './library/fpdfex.php';
require_once './library/cases_table.php';


class TPDF_Studinfo extends FPDFEx
{
    function TPDF_Studinfo()
    {
        $this->FPDFEx('StudInfo', 'L', 'A4');
        $this->Open();
    }


    function SelectFontSize($AText, $AWidths, $AFontSize, $AStep, $AMaxLines)
    {
        // try as is
        $this->SetFont('courier','',$AFontSize);
        $vText = $this->SplitText($AText, $AWidths);
        if ( count($vText) <= $AMaxLines )
            return array($vText, $AFontSize, $AStep);
        // try one line
        $AText = trim(preg_replace("/[\n\r\t ]+/", ' ', $AText));
        $vText = $this->SplitText($AText, $AWidths);
        if ( count($vText) <= $AMaxLines )
            return array($vText, $AFontSize, $AStep);
        // now try dec/ font fize....
        $vHeight = $AStep*$AMaxLines;
        for(;;)
        {
            $AFontSize = $AFontSize/1.2;
            $this->SetFont('courier','',$AFontSize);
            $vText = $this->SplitText($AText, $AWidths);
            if ( count($vText)*$this->FontSize <= $vHeight )
                return array($vText, $AFontSize, $vHeight/count($vText));
        }
    }


    function Render(&$AStudinfo)
    {
        $vDB = GetDB();
        $vName = FormatNameEx($AStudinfo);

        $this->SetMargins(0,0,0);
        $this->SetAutoPageBreak(true, 0);

        $vBranchInfo = GetBranchInfo();

        $this->AddPage();
//        $this->Image('images/studinfo.jpeg', 10, 2, 284, 201);

        # =============================================================
        # ������ ��������

        $this->SetFont('times','',10.5);
        $vHeight = $this->FontSize;
        $this->SetXY(80, 9);
        $this->ExactCell($vHeight, '��� ����� �� ���� __________');
        $this->SetXY(80, 13);
        $this->ExactCell($vHeight, '��� ���������� �� ���� ______');
        $this->Line(12, 19, 135, 19);
        $this->Line(12, 24,  85, 24);
        $this->Line(12, 32, 135, 32);
        $this->Line(85, 19,  85, 32);
        $this->SetXY(12, 19);
        $this->Cell(85-12, 24-19, '������������ ��������������� ����', '', 0, 'C');
        $this->SetXY(12, 24);
        $this->SetFont('times','',9.5);
        $this->Cell(85-12, 2, '������������ ����������', '', 0, 'C');

        $this->SetFont('courier','', 10.5);
        $this->SetXY(12, 25);
        $this->MultiCell(85-12, 3.5, iconv("utf8","windows-1251",@$vBranchInfo['name']), 0, 'L');

        $this->SetFont('times','',10.5);
        $this->SetXY(85, 19);
        $this->MultiCell(135-85, (32-19)/4, "����������� ������������\n����� � 095/�\n���. ���������� ����\n04.10.80 � 1030",0,'C');

        $this->SetFont('times','B',15.5);
        $this->SetXY(21, 35);
        $this->ExactCell($this->FontSize, '����������� ����� � ������� � _________');

        $this->SetFont('times','',11.5);
        $this->SetXY(11, 44);
        $this->ExactCell($this->FontSize, '���� ������  ');
        $vY = $this->GetY()+$this->FontSize;
        $this->Line($this->GetX(), $vY,  135, $vY);

        $this->SetFont('courier','', 12);
        $this->SetXY(55, 44);
//        $this->ExactCell($this->FontSize, Date2ReadableLong($vDB->ConvertToDate(time())));
//        $this->ExactCell($this->FontSize, Date2ReadableLong($vDB->ConvertToDate(@$AStudinfo['date'])));
        $this->ExactCell($this->FontSize, iconv("utf8","windows-1251",Date2ReadableLong(@$AStudinfo['date'])));

        $this->SetFont('times','',11.5);
        $this->SetXY(11, 51);
        $this->ExactCell($this->FontSize, '�������, ���, �������� ');
        $vY = $this->GetY()+$this->FontSize;
        $this->Line($this->GetX(), $vY,  135, $vY);
        $this->Line(12, $vY+7,  135, $vY+7);

        $this->SetFont('courier','', 12);
        $this->SetXY(55, 49);
        $this->MultiCell(135-50, 7, iconv("utf8","windows-1251",$vName), 0, 'L');

        $this->SetFont('times','',11.5);
        $this->SetXY(11, 64);
        $this->ExactCell($this->FontSize, '�������� �������� ���������, �������� ����������� ����������');
        $vY = $this->GetY()+$this->FontSize+7;
        $this->Line(12, $vY,  135, $vY);
        $vY += 7;
        $this->Line(12, $vY,  135, $vY);
        $vY += 7;
        $this->Line(12, $vY,  135, $vY);

        $this->SetFont('courier','', 12);
        $this->SetXY(11, 69);
        $this->MultiCell(135-11, 7, iconv("utf8","windows-1251",@$AStudinfo['studinfo_target']), 0, 'L');

        $this->SetFont('times','',11.5);
        $this->SetXY(11, 91);
        $this->ExactCell($this->FontSize, '������� ����������� ');
        $vY = $this->GetY()+$this->FontSize;
        $this->Line($this->GetX(), $vY,  135, $vY);
        $vY += 7;
        $this->Line(12, $vY,  135, $vY);
        $vY += 7;
        $this->Line(12, $vY,  135, $vY);
        $vY += 7;
        $this->Line(12, $vY,  135, $vY);
        $vY += 7;
        $this->Line(12, $vY,  135, $vY);


        list($vDiagnosis, $vFontSize, $vStep) = $this->SelectFontSize(iconv("utf8","windows-1251",@$AStudinfo['diagnosis']), array(87, 125), 12, 7, 5);
//            $this->SetFont('courier','',$vFontSize);
//            $vTexts = $this->SplitText(@$AStudinfo['diagnosis'], array(87, 125));
        $vX1 = 50;
        $vY1 = 91;
        for( $i=0; $i<count($vDiagnosis); $i++ )
        {
            $this->SetXY($vX1, $vY1);
            $this->ExactCell($this->FontSize, iconv("utf8","windows-1251",$vDiagnosis[$i]));
            $vX1 =  11;
            $vY1 += $vStep;
        }

        $this->SetFont('times','',11.5);
        $this->SetXY(11, 126);
        $this->ExactCell($this->FontSize, '���������� � ');
        $vY = $this->GetY()+$this->FontSize;
        $this->Line($this->GetX(), $vY,  82, $vY);

        $this->SetFont('times','',11.5);
        $this->SetXY(82, 126);
        $this->ExactCell($this->FontSize, '�� ');
        $vY = $this->GetY()+$this->FontSize;
        $this->Line($this->GetX(), $vY, 135, $vY);

        $this->SetFont('times','',11.5);
        $this->SetXY(11, 134);
        $this->ExactCell($this->FontSize, '������������');
        $this->SetXY(11, 139);
        $this->SetFont('times','',11.5);
        $this->ExactCell($this->FontSize, '     �������� � ');
        $vY = $this->GetY()+$this->FontSize;
        $this->Line($this->GetX(), $vY,  82, $vY);

        $this->SetFont('times','',11.5);
        $this->SetXY(82, 139);
        $this->ExactCell($this->FontSize, '�� ');
        $vY = $this->GetY()+$this->FontSize;
        $this->Line($this->GetX(), $vY, 135, $vY);

        $this->SetFont('times','',11.5);
        $this->SetXY(11, 149);
        $this->ExactCell($this->FontSize, '������� �����, ��������� ������� ');
        $vY = $this->GetY()+$this->FontSize;
        $this->Line($this->GetX(), $vY,  135, $vY);
        $vY += 7;
        $this->Line(12, $vY,  135, $vY);

        $this->SetFont('courier','',12);
        $this->SetXY($this->GetX(), 149);
        $this->ExactCell($this->FontSize,
                         iconv("utf8","windows-1251",iconv("utf8","windows-1251",FormatUserName(@$AStudinfo['doctor_id']))));

        $this->SetFont('times','',11.5);
        $this->SetXY(11, 162);
        $this->ExactCell($this->FontSize, '����������:');
        $this->SetXY(45, 162);
        $this->ExactCell($this->FontSize, '����������� ������ ������ ��� ����� ��������');
        $this->SetXY(45, 169);
        $this->ExactCell($this->FontSize, '�������');

        $this->SetFont('times','',1);
        $this->SetXY(11, 200);
        $this->ExactCell($this->FontSize, 'A ����� ���� ��� ����� ����� ��� ������������ ������������. ��� ���������� ��� ���� ��������, � ����� �� ����� - � �������!');

        # =============================================================
        # ������ ��������:

        $vX = 145;
        $this->SetFont('times','',10.5);
        $vHeight = $this->FontSize;
        $this->SetXY($vX+80, 9);
        $this->ExactCell($vHeight, '��� ����� �� ���� __________');
        $this->SetXY($vX+80, 13);
        $this->ExactCell($vHeight, '��� ���������� �� ���� ______');
        $this->Line($vX+12, 19, $vX+135, 19);
        $this->Line($vX+12, 24, $vX+ 85, 24);
        $this->Line($vX+12, 32, $vX+135, 32);
        $this->Line($vX+85, 19, $vX+ 85, 32);
        $this->SetXY($vX+12, 19);
        $this->Cell(85-12, 24-19, '������������ ��������������� ����', '', 0, 'C');
        $this->SetXY($vX+12, 24);
        $this->SetFont('times','',9.5);
        $this->Cell(85-12, 2, '������������ ����������', '', 0, 'C');

        $this->SetFont('courier','', 10.5);
        $this->SetXY($vX+12, 25);
        $this->MultiCell(85-12, 3.5, iconv("utf8","windows-1251",@$vBranchInfo['name']), 0, 'L');

        $this->SetFont('times','',10.5);
        $this->SetXY($vX+85, 19);
        $this->MultiCell(135-85, (32-19)/4, "����������� ������������\n����� � 095/�\n���. ���������� ����\n04.10.80 � 1030",0,'C');

        $this->SetFont('times','B',16);
        $this->SetXY($vX+43, 35);
        $this->ExactCell($this->FontSize, '� � � � � � �   � _________');

        $this->SetFont('times','B',12.5);
        $this->SetXY($vX+6, 42);
        $this->MultiCell(142-6, $this->FontSize+0.5, "� ��������� ������������������ ��������, ��������� ���������,\n���������������-������������ �������, � �������, ���������\n� ������ �������� ���������� �������, ����������� �����,\n������� ���������� ���������� (������ �����������)",0,'C');

        $vY = 42+$this->FontSize;
        $vY1 = $vY + $this->FontSize+0.5;
        $vY2 = $vY1 + $this->FontSize+0.5;
        $vY3 = $vY2 + $this->FontSize+0.5;
        switch ( @$AStudinfo['studinfo_type'] )
        {
        case 1: // ������� ���
            $this->Line($vX+8, $vY, $vX+74, $vY);       // � ��������� ������������������
            $this->Line($vX+75, $vY, $vX+93, $vY);      // ��������
            break;
        case 2: // ������� ���������
            $this->Line($vX+8, $vY, $vX+74, $vY);       // � ��������� ������������������
            $this->Line($vX+95, $vY, $vX+140, $vY);     // ��������� ���������
            break;
        case 3: // ���
            $this->Line($vX+8, $vY, $vX+74, $vY);       // � ��������� ������������������
            $this->Line($vX+95, $vY, $vX+120, $vY);     // ���������
            $this->Line($vX+10, $vY1, $vX+93, $vY1);    // ���������������-������������ �������
            break;
        case 4: // ��������
            $this->Line($vX+95, $vY1, $vX+114, $vY1);   // � �������
            $this->Line($vX+75, $vY2, $vX+91,  $vY2);   // �������
            $this->Line($vX+93, $vY2, $vX+136, $vY2);   // ����������� �����
            break;
        case 5: // ����������
            $this->Line($vX+95, $vY1, $vX+114, $vY1);   // � �������
            $this->Line($vX+75, $vY2, $vX+91,  $vY2);   // �������
            $this->Line($vX+93, $vY2, $vX+121, $vY2);   // �����������
            $this->Line($vX+18, $vY3, $vX+84, $vY3);    // ������� ���������� ����������
            break;
        default:
            break;
        }

#       �� �������:
#         $this->Line($vX+116, $vY1, $vX+138, $vY1);  /* ��������� */
#         $this->Line($vX+12, $vY2, $vX+91,  $vY2);   /* ������ �������� ���������� ������� */
#         $this->Line($vX+75, $vY2, $vX+91,  $vY2);   /* ������� */

        $vOutDate = Date2ReadableLong(@$AStudinfo['date']);
//        $vOutDate = Date2ReadableLong($vDB->ConvertToDate(@$AStudinfo['date']));
//        $vOutDate = Date2ReadableLong($vDB->ConvertToDate(time()));

        if ( empty($vOutDate) )
            $vOutDateParts = array('','','');
        else
            $vOutDateParts = explode(' ', $vOutDate);

//        Trace($vOutDate);
//        Trace($vOutDateParts);

        $this->SetFont('times','', 10.5);
        $vHeight = $this->FontSize;
        $this->SetXY($vX+36, 65);
        $this->ExactCell($vHeight, '���� ������ " ');

        $this->SetFont('courier','', 12);
        $this->Cell(8, $vHeight, iconv("utf8","windows-1251",@$vOutDateParts[0]), 'B', 0, 'C');

        $this->SetFont('times','', 10.5);
        $this->ExactCell($vHeight, '"  ');

        $this->SetFont('courier','', 12);
        $this->Cell(25, $vHeight, iconv("utf8","windows-1251",@$vOutDateParts[1]), 'B', 0, 'C');

        $this->SetFont('courier','', 12);
        $this->ExactCell($vHeight, iconv("utf8","windows-1251",@$vOutDateParts[2]));
        $this->SetFont('times','', 10.5);
        $this->ExactCell($vHeight, ' ���� ');

        $this->SetFont('times','',12.5);
        $this->SetXY($vX+10, 71);
        $this->MultiCell(145-10, $this->FontSize+0.5, "��������, ���������, �������, ����������� ���������� �������-\n��� (������ �����������)",0,'L');
        $vY = 71+$this->FontSize;
        $vY1 = $vY + $this->FontSize+0.5;

        switch ( @$AStudinfo['studinfo_type'] )
        {
        case 1: // ������� ���
            $this->Line($vX+11, $vY, $vX+28, $vY);      // ��������
            break;
        case 2: // ������� ���������
            $this->Line($vX+30, $vY, $vX+51, $vY);      // ���������
            break;
        case 3: // ���
            $this->Line($vX+30, $vY, $vX+51, $vY);      // ���������
            break;
        case 4: // ��������
            $this->Line($vX+30, $vY, $vX+51, $vY);      // ���������
            break;
        case 5: // ����������
            $this->Line($vX+53, $vY, $vX+139, $vY);     // ����������� ���������� �������-
            $this->Line($vX+11, $vY1, $vX+18, $vY1);    // ���
            break;
        default:
            break;
        }

        $this->Line($vX+11, 86, $vX+135, 86);
        $this->SetXY($vX+10, 86);
        $this->SetFont('times','', 9);
        $this->Cell(135-10, $this->FontSize, '�������� �������� ���������, �����������', '', 0, 'C');

        $this->Line($vX+11, 95, $vX+135, 95);
        $this->SetXY($vX+10, 95);
        $this->SetFont('times','', 9);
        $this->Cell(135-10, $this->FontSize, '����������', '', 0, 'C');

        $this->SetFont('courier','', 12);
        $this->SetXY($vX+12, 79);
        $this->MultiCell(135-12, 9, iconv("utf8","windows-1251",@$AStudinfo['studinfo_target']), 0, 'L');

        $this->SetFont('times','',12.5);
        $this->SetXY($vX+11, 101);
        $this->ExactCell($this->FontSize, '�������, ���, �������� ');
        $vY = $this->GetY()+$this->FontSize;
        $this->Line($this->GetX(), $vY,  $vX+135, $vY);
        $this->Line($vX+12, $vY+6.5,  $vX+135, $vY+6.5);

        $this->SetFont('courier','', 12);
        $this->SetXY($vX+57, 100);
        $this->MultiCell(135-57, 6, iconv("utf8","windows-1251",$vName), 0, 'L');

        $this->SetFont('times','',12.5);
        $this->SetXY($vX+11, 114);
        $this->ExactCell($this->FontSize, '���� �������� (���, �����, ��� ����� �� ������ ���� - ����) ');
        $vY = $this->GetY()+$this->FontSize;
        $this->Line($this->GetX(), $vY,  $vX+135, $vY);
        $this->Line($vX+12, $vY+6.5,  $vX+135, $vY+6.5);

        $this->SetFont('courier','', 12);
        $this->SetXY($vX+12, $vY+6-$this->FontSize);
        $this->ExactCell($this->FontSize, iconv("utf8","windows-1251",Date2ReadableLong(@$AStudinfo['born_date'])));

        $this->SetFont('times','',12.5);
        $this->SetXY($vX+11, 127);
        $this->ExactCell($this->FontSize, '������� ����������� (������ ������� ����������) ');
        $vY = $this->GetY()+$this->FontSize;
        $this->Line($this->GetX(), $vY,  $vX+135, $vY);
        $this->Line($vX+12, $vY+6.5,  $vX+135, $vY+6.5);
        $this->Line($vX+12, $vY+6.5*2,  $vX+135, $vY+6.5*2);

        if ( iconv("utf8","windows-1251",@$AStudinfo['studinfo_show_diagnosis']) )
        {
            list($vDiagnosis, $vFontSize, $vStep) = $this->SelectFontSize(@$AStudinfo['diagnosis'], array(25, 125), 12, 6.5, 3);
            $vX1 = $vX+110;
            $vY1 = 127;
            for( $i=0; $i<count($vDiagnosis); $i++ )
            {
                $this->SetXY($vX1, $vY1);
                $this->ExactCell($this->FontSize, $vDiagnosis[$i]);
                $vX1 = $vX+11;
                $vY1 += $vStep;
            }
            $this->SetFont('times','',12.5);
            $this->SetXY($vX+10, 192);
            $this->ExactCell($this->FontSize, '����������: ������� ������ ��� ������� ��������');
        }

        $this->SetFont('times','',12.5);
        $this->SetXY($vX+11, 147);
        $this->ExactCell($this->FontSize, '������� �������� � ������������� �������� (���, ��, ������)');
        $vY = $this->GetY()+$this->FontSize;
        $this->Line($vX+105, $vY, $vX+111, $vY);   // ���

        $vY = $this->GetY()+$this->FontSize+6;
        $this->Line($vX+12, $vY,  $vX+135, $vY);
        $this->SetFont('times','',9);
        $this->SetXY($vX+11, $vY);
        $this->Cell(135-10, $this->FontSize, '(�����������, �������)', '', 0, 'C');
        $vY += 6.5;
        $this->Line($vX+12, $vY,  $vX+135, $vY);
        $this->SetFont('times','',9);
        $this->SetXY($vX+11, $vY);
        $this->Cell(135-10, $this->FontSize, '���������� �� �������, ��������� �������� ����������� ����������', '', 0, 'C');
        $vY += 7.5;
        $this->Line($vX+12, $vY,  $vX+135, $vY);

        $this->SetFont('times','',12.5);
        $this->SetXY($vX+10, 173);
        $this->ExactCell($this->FontSize, '� ');
        $vY = $this->GetY()+$this->FontSize;
        $this->Line($this->GetX(), $vY,  $vX+68, $vY);

        $this->SetFont('times','',12.5);
        $this->SetXY($vX+69, 173);
        $this->ExactCell($this->FontSize, '�� ');
        $vY = $this->GetY()+$this->FontSize;
        $this->Line($this->GetX(), $vY, $vX+135, $vY);

        $this->SetFont('times','',12.5);
        $this->SetXY($vX+10, 178);
        $this->ExactCell($this->FontSize, '� ');
        $vY = $this->GetY()+$this->FontSize;
        $this->Line($this->GetX(), $vY,  $vX+68, $vY);

        $this->SetFont('times','',12.5);
        $this->SetXY($vX+69, 178);
        $this->ExactCell($this->FontSize, '�� ');
        $vY = $this->GetY()+$this->FontSize;
        $this->Line($this->GetX(), $vY, $vX+135, $vY);

        $this->SetFont('times','',12.5);
        $this->SetXY($vX+10, 185);
        $this->ExactCell($this->FontSize, '�.�. �����������');

        $this->SetFont('times','BI',12);
        $this->SetXY($vX+64, 186);
        $this->ExactCell($this->FontSize, '������� �����  ');
        $vY = $this->GetY()+$this->FontSize;
        $this->Line($this->GetX(), $vY,  $vX+135, $vY);

        # ===============================================================
        # ����� ������
        $this->SetLineWidth(0.5);
        $this->SetDrawColor(128);
        $this->Line(149, 10,  149, 203);
    }
}


$vDoc = new TPDF_Studinfo;
$vDoc->Render($_GET);

header('Accept-Ranges: bytes');
$vDoc->Output('Studinfo.pdf', 'I');


?>

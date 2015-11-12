<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Refresh" content="300">
    <link href="../emst.css" type="text/css" rel="stylesheet">
    <link href="../favicon.ico" rel="shortcut icon">
    <title><?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'GetUserName'))) echo htmlspecialchars($t->GetUserName());?>: Врач: Самоконтроль</title>
  </head>

  <body>
    <table cellspacing="0" cellpadding="4">
      <tr>
        <td valign="top" class="maintable">
          <?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'GetMenu'))) echo $t->GetMenu();?>
        </td>
        <td valign="top">
          <h1>Самоконтроль</h1>
          <?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'GetTable'))) echo $t->GetTable();?>
          <?php if ($this->options['strict'] || (isset($t->form) && method_exists($t->form, 'outputHeader'))) echo $t->form->outputHeader();?>
            <?php echo $t->form->hidden;?>
            <table class=maintable>
              <tr>
                <td colspan="6"><hr></td>
              </tr>
              <tr>
                <th colspan="6"><?php echo $t->form->header->Header;?></th>
              </tr>
              <tr>
                <td class="label"> <?php echo $t->form->case_id->label;?> </td>
                <td class="control"> <?php echo $t->form->case_id->html;?>  </td>
                <td class="label"> <?php echo $t->form->doctor_id->label;?> </td>
                <td class="control"> <?php echo $t->form->doctor_id->html;?>  </td>
                <td colspan="2" class="control">&nbsp;</td>
              </tr>
              <tr>
                <td class="label"> <?php echo $t->form->last_name->label;?> </td>
                <td class="control"> <?php echo $t->form->last_name->html;?>  </td>
                <td class="label"> <?php echo $t->form->first_name->label;?> </td>
                <td class="control"> <?php echo $t->form->first_name->html;?>  </td>
                <td class="label"> <?php echo $t->form->patr_name->label;?> </td>
                <td class="control"> <?php echo $t->form->patr_name->html;?>  </td>
              </tr>
              <tr>
                <td class="label"> <?php echo $t->form->beg_date->label;?> </td>
                <td class="control"> <?php echo $t->form->beg_date->html;?>  </td>
                <td class="label"> <?php echo $t->form->end_date->label;?> </td>
                <td class="control"> <?php echo $t->form->end_date->html;?>  </td>
                <td class="label"> &nbsp; </td>
                <td class="control"> &nbsp; </td>
              </tr>
              <tr>
                <td class="label"> <?php echo $t->form->show_diagnosis->label;?> </td>
                <td class="control"> <?php echo $t->form->show_diagnosis->html;?>  </td>
                <td class="label"> <?php echo $t->form->show_cure->label;?> </td>
                <td class="control"> <?php echo $t->form->show_cure->html;?>  </td>
                <td class="label"> &nbsp; </td>
                <td class="control"> &nbsp; </td>
              </tr>
              <tr>
                <td colspan="6" align="right"><?php echo $t->form->Submit->html;?></td>
              </tr>
            </table>
          </form>

        </td>
      </tr>
    </table>
  </body>
</html>

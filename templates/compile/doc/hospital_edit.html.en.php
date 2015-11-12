<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href="../emst.css" type="text/css" rel="stylesheet">
    <link href="../favicon.ico" rel="shortcut icon">
    <title><?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'GetUserName'))) echo htmlspecialchars($t->GetUserName());?>: Врач: Сведения из стационара</title>
  </head>

  <body>
    <table cellspacing="0" cellpadding="4">
      <tr>
        <td valign="top" class="maintable">
          <?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'GetMenu'))) echo $t->GetMenu();?>
        </td>
        <td valign="top">
          <?php if ($this->options['strict'] || (isset($t->form) && method_exists($t->form, 'outputHeader'))) echo $t->form->outputHeader();?>
            <?php echo $t->form->hidden;?>
            <table class=maintable>
              <tr><th colspan="2"><?php echo $t->form->header->header;?></th></tr><tr>
              <tr><td class="label"> <?php echo $t->form->beg_date->label;?>        </td><td class="control"> <?php echo $t->form->beg_date->html;?>        </td></tr>
              <tr><td class="label"> <?php echo $t->form->end_date->label;?>        </td><td class="control"> <?php echo $t->form->end_date->html;?>        </td></tr>
              <tr><td class="label"> <?php echo $t->form->name->label;?>            </td><td class="control"> <?php echo $t->form->name->html;?>            </td></tr>
              <tr><td class="label"> <?php echo $t->form->diagnosis->label;?>       </td><td class="control"> <?php echo $t->form->diagnosis->html;?>       </td></tr>
              <tr><td class="label"> <?php echo $t->form->operation->label;?>            </td><td class="control"> <?php echo $t->form->operation->html;?>  </td></tr>

              <tr><td class="label"> <?php echo $t->form->recommendation->label;?>  </td><td class="control"> <?php echo $t->form->recommendation->html;?>  </td></tr>
              <tr><td class="label"> <?php echo $t->form->notes->label;?>           </td><td class="control"> <?php echo $t->form->notes->html;?>            </td></tr>
              <tr><td>&nbsp;</td>                                          <td align="right"> <?php echo $t->form->do_Save->html;?> <?php echo $t->form->do_Cancel->html;?> </td>
              </tr>
            </table>
<!--            <table>
              <tr><td>{form.requirednote:h}</td></tr>
            </table>
-->
          </form>
        </td>
      </tr>
    </table>
  </body>
</html>

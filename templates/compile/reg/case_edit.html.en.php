<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href="../emst.css" type="text/css" rel="stylesheet">
    <link href="../favicon.ico" rel="shortcut icon">
    <link rel="stylesheet" href="../scripts/jquery-ui-1.10.4.custom/css/smoothness/jquery-ui-1.10.4.custom.css">
    <script src="../scripts/jquery/js/jquery-1.8.3.js"></script>
    <script src="../scripts/jquery/js/jquery-ui-1.9.2.custom.js"></script>

    <title><?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'GetUserName'))) echo htmlspecialchars($t->GetUserName());?>: Регистратура: история болезни</title>
      <style>
          .ui-corner-alll{width: 98%;height:205px;border:1px solid black; border-radius: 5px;}
          .error{
              background-color: #ff0000;
          }
      </style>

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
              <tr><th colspan="8"><?php echo $t->form->header->header1;?></th></tr><tr>
              <tr><td class="label"> <?php echo $t->form->last_name->label;?>       </td><td class="control" colspan="7"> <?php echo $t->form->last_name->html;?>       </td></tr>
              <tr><td class="label"> <?php echo $t->form->first_name->label;?>      </td><td class="control" colspan="7"> <?php echo $t->form->first_name->html;?>      </td></tr>
              <tr><td class="label"> <?php echo $t->form->patr_name->label;?>       </td><td class="control" colspan="7"> <?php echo $t->form->patr_name->html;?>       </td></tr>

              <tr>

                  <td class="control" colspan="8">
                  <div>
                      <fieldset class="ui-corner-alll" id="patientResulteFset" style="visibility: visible; background-color:#ffffff;">
                          <legend>Найденные пациенты</legend>
                          <div id="patientResult" style="height:200px;overflow:auto;"></div>
                      </fieldset>
                   </div>
                  </td>
              </tr>
       
              <tr><td align="right" colspan="8">   <?php echo $t->form->Submit->html;?>          </td>
              </tr>
            </table>
            <table>
              <tr><td><?php echo $t->form->requirednote;?></td></tr>
            </table>
          </form>
        </td>
      </tr>
    </table>
  </body>
  <script>
      $("input[name='last_name']").autocomplete({
          source: function( request, response ) {
              $.post('autocompl_lastname.php',
                      {
                          term        : request.term,
                          field       : 'lastName',
                          filter1_name: 'firstName',
                          filter1     : $("input[name='first_name']").val(),
                          filter2_name: 'patrName',
                          filter2     : $("input[name='patr_name']").val()
                      },
                      function (data)
                      {
                          response(data.split("\n"));
                      }
              );
          },
          minLength: 0,
          select: function(event, ui) {
              console.log(ui.item ?
                      "Selected: " + ui.item.value + " aka " + ui.item.id :
                      "Nothing selected, input was " + this.value
              );
          }
      });
      $("input[name='first_name']").autocomplete({
          source: function( request, response ) {
              $.post('autocompl_lastname.php',
                      {
                          term        : request.term,
                          field       : 'firstName',
                          filter1_name: 'lastName',
                          filter1     : $("input[name='last_name']").val(),
                          filter2_name: 'patrName',
                          filter2     : $("input[name='patr_name']").val()
                      },
                      function (data)
                      {
                          response(data.split("\n"));
                      }
              );
          },
          minLength: 0,
          select: function(event, ui) {
              console.log(ui.item ?
                      "Selected: " + ui.item.value + " aka " + ui.item.id :
                      "Nothing selected, input was " + this.value
              );
          }
      });
      $("input[name='patr_name']").autocomplete({
          source: function( request, response ) {
              $.post('autocompl_lastname.php',
                      {
                          term        : request.term,
                          field       : 'patrName',
                          filter1_name: 'firstName',
                          filter1     : $("input[name='first_name']").val(),
                          filter2_name: 'lastName',
                          filter2     : $("input[name='last_name']").val()
                      },
                      function (data)
                      {
                         response(data.split("\n"));
                      }
              );
          },
          minLength: 0,
          select: function(event, ui) {
              console.log(ui.item ?
                      "Selected: " + ui.item.value + " aka " + ui.item.id :
                      "Nothing selected, input was " + this.value
              );
          }
      });

      //$('').
      $('#patientResult').delegate('tr', 'click', function(e) {
          var id = $(e.target).closest('tr').data('id');
          console.log('You chosen patient:', id);

          $.post('filling_field.php', {
              'id': id
          }, function(data) {
              data = data.split('#');
              console.log(data);

              $("input[name='last_name']").val(data[1]);
              $("input[name='first_name']").val(data[2]);
              $("input[name='patr_name']").val(data[3]);
              

          });
      });
  </script>
</html>

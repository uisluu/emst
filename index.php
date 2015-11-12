<?php

  #####################################################################
  #
  # ����������. (c) 2005 Vista
  #
  #####################################################################

    require_once 'library/common.php';

    session_set_cookie_params( time()+9999999 );
    
//    if ( array_key_exists(session_name(),$_GET) )
//      session_id( $_GET[session_name()] );

    session_start();
    ob_start();


    if ( @(isset($_SESSION['User.ID']) && $_SESSION['User.ID']) )
    {

        $vPage = @$_GET['page'];
        if ( $vPage == '' )
        {
            $vPage = @$_SESSION['User.FirstPage'];
            if ( $vPage != '' )
              Redirect( $vPage );
        }
        $vPageFile = $vPage.'.php';
        if ( $vPage!='' && is_file($vPageFile) )
        {
            if ( (!preg_match('/_pdf$/i', $vPage)) &&
                 ( empty($_SESSION['PrevPage']) ||
                   $_SERVER['REQUEST_URI']!=@$_SESSION['ThisPage']) )
            {
                $_SESSION['PrevPage'] = @$_SESSION['ThisPage'];
                $_SESSION['ThisPage'] = $_SERVER['REQUEST_URI'];
            }
            include($vPageFile);
        }
        else
        {
            ob_end_clean();
//            header("HTTP/1.0 404 Not Found");
            print "page not found:". $vPageFile;
        }
    }
    else
    {
        include('login.php');
    }

?>

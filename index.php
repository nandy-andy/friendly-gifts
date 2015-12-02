<?php
session_start();

require_once 'xmasusers.class.php';
require_once 'view.functions.php';

$xu = new XmasUsers();
$xu->connect();

if( isset($_GET['logout']) )
{
    unset($_SESSION['user_id']);
}

if( isset($_POST['ok']) && 'ok' === $_POST['ok'] )
{
//proba logowania
    if( empty($_POST['login']) )
    {
        $sError = 'Podaj login';
        $iUserId = 0;
    }
    elseif( empty($_POST['password']) )
    {
        $loginTyped = $_POST['login'];
        $sError = 'Podaj hasło';
        $iUserId = 0;
    }
    else
    {
        if( false !== ($iUserId = $xu->doLogin($_POST['login'], $_POST['password'])) )
        {
            $iUserId = intval($iUserId);
            $_SESSION['user_id'] = $iUserId;
        }
        else 
        {
            $sError = 'Nieprawidłowy login lub hasło';
        }
    }
}
else
{
//lista
    if( empty($_SESSION['user_id']) )
    {
        $iUserId = 0;
    }
    else 
    {
       //uzytkownik zalogowany
       $iUserId = $_SESSION['user_id'];
    }
}

if( is_int($iUserId) && 0 < $iUserId )
{
//jesli zalogowany
    $usersList = $xu->getUsersList();
    $drawnUsers = $xu->countDrawnUsers();
    $logged = $xu->getUserInfo($iUserId);
    
    if( '0' === $logged['drawn'] )
    {
        $drawnUser = array();
    }
    else
    {
        $drawnUser = $xu->getUserInfo($logged['drawn']);
    }
}

if( isset($_POST['losuj']) )
{
    if( 0 < $iUserId )
    {
        //losowanie
        $drawn = $xu->drawUser($iUserId);
    }
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Program Losujący - (Nie-)Totalizatora Sportowego</title>
<link rel="stylesheet" type="text/css" media="screen" href="style.css" />
</head>
<body>
    <div id="content">
        <?php if( !empty($sError) ): ?>
            <div id="error"><?php echo $sError; ?></div>
        <?php endif; ?>
        
        <?php if( is_int($iUserId) && 0 < $iUserId ): ?>
            <?php if( isset($_POST['losuj']) ): ?>
                <?php showDrawingPane($drawn); ?>
            <?php else: ?>
                <div id="users">
                    <?php showUsersList($usersList, $drawnUsers); ?>
                </div>
                
                <div id="loggedInfo">
                    <?php showLoggedUserPane($logged); ?>
                </div>
                
                <div id="drawnUser">
                    <?php showDrawnUserPane($drawnUser, $iUserId); ?>
                </div>
            <?php endif; ?>
        <?php else:
            showLoginPane();
        endif;?>
        <img id="gifts_pic" alt="gifts" src="gifts.png">
    </div>
</body>
<?php if( isset($_POST['losuj']) ): ?>
<script type="text/javascript">
    function delayer()
    {
        window.location = './index.php';
    }
    
    setTimeout('delayer()', 5000);
</script>
<?php endif; ?>
</html>
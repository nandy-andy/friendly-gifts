<?php
function showLoginPane($loginTyped = '')
{
?>
<form action="./index.php" method="post" id="login">
    <fieldset>
        <table>
            <tr>
                <td>Login:</td>
                <td><input type="text" name="login" value="<?php echo $loginTyped; ?>" /></td>
            </tr>
            <tr>
                <td>Hasło:</td>
                <td><input type="password" name="password" value="" /></td>
            </tr>
            <tr>
                <td></td>
                <td><input type="submit" name="ok" value="ok" /></td>
            </tr>
        </table>
    </fieldset>
</form>
<?php 
}

function showDrawnUserPane($drawnUser = array())
{
?>
<?php if( !empty($drawnUser) ): ?>
    Kupisz prezent użytkownikowi: <strong><?php echo $drawnUser['name']; ?></strong>
<?php else: ?>
    <form action="./index.php" method="post" id="draw">
        <fieldset>
            Jeszcze nikogo nie wylosowałeś. <input type="submit" id="drawBtn" name="losuj" value="Losuj teraz!" />
        </fieldset>
    </form>
<?php endif; ?>
<?php 
}

function showLoggedUserPane($logged)
{
?>
Zalogowany jako: <?php echo $logged['name'].' ['.$logged['login'].']'; ?> <a href="?logout">Wyloguj</a>
<?php 
}

function showUsersList($usersList, $drawnUsers = 0)
{
?>
    <span>Użytkownicy:</span>
    <table id="users_table">
        <?php foreach($usersList as $user): ?>
            <tr><td><?php echo $user['name']; ?></td></tr>
        <?php endforeach;?>
    </table>
    <span class="small">Wylosowani: <?php echo (empty($drawnUsers) ? '0' : $drawnUsers); ?></span>
<?php 
}

function showDrawingPane($drawn)
{
?>
<?php if( -1 == $drawn ): ?>
    <div id="drawn">Już brał(eś/aś) udział w losowaniu</div>
<?php elseif( -2 == $drawn ): ?>
    <div id="drawn">Niestety nie ma już nikogo, komu mógłbyś/mogłabyś kupić prezent</div>
<?php elseif( -3 == $drawn ): ?>
    <div id="drawn">Podjęto trzy próby wylosowania użytkownika i niestety nie ma już nikogo, komu mógłbyś/mogłabyś kupić prezent</div>
<?php elseif( false === $drawn ): ?>
    <div id="drawn">Wystąpił błąd bazy danych</div>
<?php else: ?>
    <div id="drawn">Kupisz prezent użytkownikowi: <br /><strong><?php echo $drawn['name']; ?></strong></div>
<?php endif;?>
<?php 
}
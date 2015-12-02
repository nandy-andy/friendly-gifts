<?php
class XmasUsers 
{
    private $pdo = null;
    private $users_table = 'xg_users';
    
    public function connect() {
        if( is_null($this->pdo) ) {
            try {
                $this->pdo = new PDO('mysql:dbname=INSERT_HERE_YOU_DB_NAME;host=INSERT_HERE_YOUR_DB_HOST', 'INSERT_HERE_YOUR_DB_USER', 'INSERT_HERE_YOUR_DB_PASSWORD');
                $this->pdo->exec("SET CHARACTER SET utf8");
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch(PDOException $e) {
                die('Database connection failed: '.$e->getMessage());
            } catch(Exception $e) {
                die('Something went wrong: '.$e->getMessage());
            }
        }
    }
    
    public function getUsersList() {
        $sql = 'select name from '.$this->users_table.' order by name';
        $res = array();
        
        try {
            $stm = $this->pdo->prepare($sql);
            $stm->execute();
            $res = $stm->fetchAll();
        } catch(Exception $e) {
            $res['error'] = $e->getMessage();
        }
        
        return $res;
    }
    
    public function doLogin($login, $pass) {
        $login = strip_tags($login);
        $pass = strip_tags($pass);
        $sql = 'select id from '.$this->users_table.' where login = ? and password = md5(?)';
        
        try {
            $stm = $this->pdo->prepare($sql);
            $stm->bindParam(1, $login, PDO::PARAM_STR);
            $stm->bindParam(2, $pass, PDO::PARAM_STR);
            $stm->execute();
            
            $res = $stm->fetchAll();
            if( empty($res[0]['id']) )
            {
                $res = false;
            }
            else
            {
               $res = $res[0]['id'];
            }
        } catch(Exception $e) {
            $res = false;
        }
        
        return $res;
    }
    
    public function getUserInfo($id) {
        $sql = 'select name, login, drawn from '.$this->users_table.' where id = ?';
        
        try {
            $stm = $this->pdo->prepare($sql);
            $stm->bindParam(1, $id, PDO::PARAM_INT);
            $stm->execute();
            
            $res = $stm->fetchAll();
            if( empty($res[0]) )
            {
                $res = false;
            }
            else
            {
               $res = $res[0];
            }
        } catch(Exception $e) {
            $res = false;
        }
        
        return $res;
    }
    
    public function countDrawnUsers() {
        $sql = 'select count(id) as drawn from '.$this->users_table.' where drawn_by is not null';
        
        try {
            $stm = $this->pdo->prepare($sql);
            $stm->execute();
            
            $res = $stm->fetch();
            if( empty($res['drawn']) ) {
                $res = false;
            } else {
               $res = $res['drawn'];
            }
        } catch(Exception $e) {
            $res = false;
        }
        
        return $res;
    }
    
    /**
     * Losuje użytkownika do kupna prezentu
     * 
     * @param $id
     * 
     * @return -1 gdy już istnieje wylosowany użytkownik
     * @return -2 gdy nie ma już użytkowników do losowania
     * @return -3 gdy podjeto 3 proby wylosowania uzytkownika i zadna sie nie powiodla
     * @return false w przypadku błędu/wyjątku w bazie danych
     */
    public function drawUser($id) {
        $sql = 'select drawn from '.$this->users_table.' where id = ?';
        
        try {
            $stm = $this->pdo->prepare($sql);
            $stm->bindParam(1, $id, PDO::PARAM_INT);
            $stm->execute();
            
            $res = $stm->fetch();
            
            if( empty($res['drawn']) ) {
                $bAbleToDrawn = true;
            } else {
                $bAbleToDrawn = false;
            }
        } catch(Exception $e) {
            $res = false;
        }
        
        if( true === $bAbleToDrawn ) {
            $res = $this->draw($id);
            $repeatNo = 1;
            while( false === $this->saveDrawing($id, $res['id']) ) {
                if( 3 <= $repeatNo ) {
                    $res = $this->draw($id);
                } else {
                    $res = -3;
                }
            }
        } else {
            $res = -1;
        }
        
        return $res;
    }
    
    private function draw($id) {
        $sql = 'select id, name from '.$this->users_table.' where drawn_by is null and id != ? order by rand()';
        
        try {
            $stm = $this->pdo->prepare($sql);
            $stm->bindParam(1, $id, PDO::PARAM_INT);
            $stm->execute();
            
            $res = $stm->fetch();
            
            if( empty($res['id']) )
            {
                $res = -2;
            }
        } catch(Exception $e) {
            $res = false;
        }
        
        return $res;
    }
    
    private function saveDrawing($id, $drawnId) {
        try {
            $this->pdo->beginTransaction();
            
            $sql = 'update '.$this->users_table.' set drawn = ?, drawn_on = ? where id = ?';
            $drawnOn = date('Y-m-d H:i:s');
            
            $stm = $this->pdo->prepare($sql);
            $stm->bindParam(1, $drawnId, PDO::PARAM_INT);
            $stm->bindParam(2, $drawnOn, PDO::PARAM_STR);
            $stm->bindParam(3, $id, PDO::PARAM_INT);
            $stm->execute();
            
            $sql = 'update '.$this->users_table.' set drawn_by = ?, drawn_by_on = ? where id = ?';
            
            $stm = $this->pdo->prepare($sql);
            $stm->bindParam(1, $id, PDO::PARAM_INT);
            $stm->bindParam(2, $drawnOn, PDO::PARAM_STR);
            $stm->bindParam(3, $drawnId, PDO::PARAM_INT);
            $stm->execute();
            
            $this->pdo->commit();
            
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            
            if( '23000' === $e->getCode() )
            {
               return false;
            }
            else
            {
               die('Failure: '.$e->getMessage());
            }
         }
    }
}

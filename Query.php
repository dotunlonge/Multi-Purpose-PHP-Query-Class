<?php

/*
 *@author
 * Oludotun Williams Longe
 * Fullstack Developer
 * oludotunlonge@gmail.com
 * 10th August, 2017
 */

class DbQuery
{
    private $conn;

    public function __construct(){

        try {
            $this->conn = new PDO("mysql:host=localhost;dbname=put-your-db-name-here;charset=utf8mb4", 'root', 'root');
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function lastId(){
        return $this->conn->lastInsertId();
    }

    /**
     * @param $whatTable
     * @param $whichColumns
     * @param $whatValues
     * @return null|string = yes for insert successfull and no insert unsuccessful
     *
     * Syntax: insert[into what table, into which columns, exactly what values?] e.g
     * insert into["users",["username","password"],["$_POST["username"], $_POST["password"]]]
     */

    public function insert($whatTable, $whichColumns, $whatValues){
        $returnMe = null;
        $insertQuestionMarks = array();
        for($i=1; $i <= count($whichColumns); $i++){
            $insertQuestionMarks[$i] = "?";
        }
        $insertQuestionMarks = implode(",", $insertQuestionMarks);
        switch (is_array($whichColumns)){
            case true:
                $whichColumns = implode(",", $whichColumns);
                break;
        }
        try{

            $queryString =  "INSERT INTO " . $whatTable . " ( " . $whichColumns . " ) VALUES ( " . $insertQuestionMarks . " ) ";
            $stmt = $this->conn->prepare($queryString);
            $stmt->execute($whatValues);
            $didThisExecute = $stmt->rowCount();

            switch($didThisExecute > 0){
                case true: $returnMe =  true;
                break;
                case false: $returnMe =  false;
                break;
            }
        }catch (PDOException $e){
            $returnMe = $e->getMessage();

            if(preg_match('/Duplicate/',$returnMe)){
                $returnMe = 'duplicate';
            }
        }
     return $returnMe;

    }

    /**
     * @param $selectWhat -> string
     * @param $whichTable -> string
     * @param null $joinArray -> array
     * @param $XEqualsY -> array
     * @return array|null|string -> it returns both the data and a boolean if successsful in this order [true boolean, sqlData];
     *                              and simple false if unsuccessful.
     *
     * For Simple Select Queries that is, queries without join:
     * Syntax: select[what variable/value/string are you looking for in the db,
     * what table should it search from,null, What Conditions Should The Search Take into Consideration] e.g
     *
     * select["username","users",null, ["username => "shawn", "age => "25"]]
     *
     * For JOIN Queries
     *
     * select["username","users",["inner/left/right/", "books ON users.id = books.userId", "money ON users.id = money.userId"], ["users.username => "shawn", "books.age => "25"]]
     *
     *
     */

    public function select($selectWhat, $FromWhichTable,$joinArray = null, $WhereXEqualsY){

        $returnMe = null;
        $queryString = null;

        switch (is_array($selectWhat)){
            case true:
                $selectWhat = implode(",", $selectWhat);
                break;
        }
        $argumentsString = [];
        $executeArray = [];

        foreach ($WhereXEqualsY as $key => $value){
            array_push($argumentsString, $key . " = ? ");
            array_push($executeArray, $value);
        }

        $argumentsString = implode(" AND ", $argumentsString);

        switch ($joinArray == null) {
            case true:
                $queryString = "SELECT ". $selectWhat ." FROM ". $FromWhichTable ." WHERE ". $argumentsString;
                break;

            case false:
                $joinType = trim(strtolower(array_shift($joinArray)));
                $queryString = "SELECT ". $selectWhat ." FROM ". $FromWhichTable ;

                switch ($joinType){
                    case "inner":
                        $joinQuery = implode(" INNER JOIN ",$joinArray);
                        $joinQuery =  " INNER JOIN ".$joinQuery;
                        $queryString = $queryString.$joinQuery." WHERE ". $argumentsString;
                        break;

                    case "left":
                        $joinQuery = implode(" LEFT OUTER JOIN ",$joinArray);
                        $joinQuery =  " LEFT OUTER JOIN ".$joinQuery;
                        $queryString = $queryString.$joinQuery." WHERE ". $argumentsString;
                        break;

                    case "right":
                        $joinQuery = implode(" RIGHT OUTER JOIN ",$joinArray);
                        $joinQuery =  " RIGHT OUTER JOIN ".$joinQuery;
                        $queryString = $queryString.$joinQuery." WHERE ". $argumentsString;
                        break;
                }
                break;
        }

        try {
            $stmt = $this->conn->prepare($queryString);
            $stmt->execute($executeArray);
            $count = $stmt->rowCount();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($count > 0 && $result) {
                $returnMe = [true, $result];
            }else{
                $returnMe = false;

            }
        }catch (PDOException $e){
            $returnMe = $e->getMessage();
        }
        return $returnMe;
    }

    public function conditionBasedSelectAll($selectWhat, $FromWhichTable,$joinArray = null, $WhereXEqualsY){

        $returnMe = null;
        $queryString = null;

        switch (is_array($selectWhat)){
            case true:
                $selectWhat = implode(",", $selectWhat);
                break;
        }
        $argumentsString = [];
        $executeArray = [];

        foreach ($WhereXEqualsY as $key => $value){
            array_push($argumentsString, $key . " = ? ");
            array_push($executeArray, $value);
        }

        $argumentsString = implode(" AND ", $argumentsString);

        switch ($joinArray == null) {
            case true:
                $queryString = "SELECT ". $selectWhat ." FROM ". $FromWhichTable ." WHERE ". $argumentsString;
                break;

            case false:
                $joinType = trim(strtolower(array_shift($joinArray)));
                $queryString = "SELECT ". $selectWhat ." FROM ". $FromWhichTable ;

                switch ($joinType){
                    case "inner":
                        $joinQuery = implode(" INNER JOIN ",$joinArray);
                        $joinQuery =  " INNER JOIN ".$joinQuery;
                        $queryString = $queryString.$joinQuery." WHERE ". $argumentsString;
                        echo $queryString;
                        break;

                    case "left":
                        $joinQuery = implode(" LEFT OUTER JOIN ",$joinArray);
                        $joinQuery =  " LEFT OUTER JOIN ".$joinQuery;
                        $queryString = $queryString.$joinQuery." WHERE ". $argumentsString;
                        break;

                    case "right":
                        $joinQuery = implode(" RIGHT OUTER JOIN ",$joinArray);
                        $joinQuery =  " RIGHT OUTER JOIN ".$joinQuery;
                        $queryString = $queryString.$joinQuery." WHERE ". $argumentsString;
                        break;
                }
                break;
        }

        try {
            $stmt = $this->conn->prepare($queryString);
            $stmt->execute($executeArray);
            $count = $stmt->rowCount();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($count > 0 && $result) {
                $returnMe = [true, $result];
            }else{
                $returnMe = false;

            }
        }catch (PDOException $e){
            $returnMe = $e->getMessage();
        }
        return $returnMe;
    }

    public function selectAll($selectWhat, $FromWhichTable,$joinArray = null){
        $returnMe = null;
        $queryString = null;

        switch (is_array($selectWhat)){
            case true:
                $selectWhat = implode(",", $selectWhat);
                break;
        }
        $executeArray = [];

        switch ($joinArray == null) {
            case true:
                $queryString = "SELECT ". $selectWhat ." FROM ". $FromWhichTable ;
                break;

            case false:
                $joinType = trim(strtolower(array_shift($joinArray)));
                $queryString = "SELECT ". $selectWhat ." FROM ". $FromWhichTable ;

                switch ($joinType){
                    case "inner":
                        $joinQuery = implode(" INNER JOIN ",$joinArray);
                        $joinQuery =  " INNER JOIN ".$joinQuery;
                        $queryString = $queryString.$joinQuery;
                        echo $queryString;
                        break;

                    case "left":
                        $joinQuery = implode(" LEFT OUTER JOIN ",$joinArray);
                        $joinQuery =  " LEFT OUTER JOIN ".$joinQuery;
                        $queryString = $queryString.$joinQuery;
                        break;

                    case "right":
                        $joinQuery = implode(" RIGHT OUTER JOIN ",$joinArray);
                        $joinQuery =  " RIGHT OUTER JOIN ".$joinQuery;
                        $queryString = $queryString.$joinQuery;
                        break;
                }
                break;
        }

        try {
            $stmt = $this->conn->prepare($queryString);
            $stmt->execute($executeArray);
            $count = $stmt->rowCount();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($count > 0 && $result) {
                $returnMe = [true, $result];
            }else{
                $returnMe = false;

            }
        }catch (PDOException $e){
            $returnMe = $e->getMessage();
        }
        return $returnMe;
    }

    /**
     * @param $fromWhatTable -> string -> The Table You Want To Delete From
     * @param $WhereXEqualsY -> array -> The Condition To Take Into Consideration During Deletion
     * @return null|string -> returns a simple yes or no
     *
     * Syntax : delete['users', ['age'=> '20', 'name' => 'shawn']]
     *
     */
    public function delete($fromWhatTable, $WhereXEqualsY){
        $returnMe = null;
        $argumentsString = [];
        $executeArray = [];

        foreach ($WhereXEqualsY as $key => $value){
            array_push($argumentsString, $key . " = ? ");
            array_push($executeArray, $value);
        }

        $argumentsString = implode(" AND ", $argumentsString);


        try{
            $queryString =  "DELETE FROM " . $fromWhatTable . " WHERE " .  $argumentsString  ;

            $stmt = $this->conn->prepare($queryString);
            $stmt->execute($executeArray);
            $didThisExecute = $stmt->rowCount();
            switch($didThisExecute > 0){
                case true: $returnMe =  true; break;
                case false: $returnMe =  false;break;
            }
        }catch (PDOException $e){
            $returnMe = $e->getMessage();
        }
        return $returnMe;

    }

    /**
     * @param $updateWhatTable -> string
     * @param $setWhatToWhat -> array
     * @param $WhereXEqualsY -> array
     * @return null|string
     *
     * Syntax update("user", ['age' => '12', 'name'=> 'tami'], ['code' => '123', 'gender' => 'male']")
     */

    public function update($updateWhatTable,$setWhatToWhat, $WhereXEqualsY){

        $returnMe = null;
        $argument1= [];
        $argument2= [];

        $execute= [];

        foreach ($setWhatToWhat as $key => $value){
            array_push($argument1, $key." = ?");
            array_push($execute, $value);
        }

        $argumentSet = implode(" , ", $argument1);


        foreach ($WhereXEqualsY as $key => $value){
            array_push($argument2, $key." = ?");
            array_push($execute, $value);
        }

        $argumentWhere = implode(" AND ", $argument2);

        try{
            $queryString = "UPDATE " . $updateWhatTable . " SET ". $argumentSet . " WHERE ". $argumentWhere;
            $stmt = $this->conn->prepare($queryString);
            $stmt->execute($execute);
            $didThisExecute = $stmt->rowCount();
            switch($didThisExecute > 0){
                case true: $returnMe =  true; break;
                case false: $returnMe =  false;break;
            }
        }catch (PDOException $e){
            $returnMe = $e->getMessage();
        }


        return $returnMe;
    }

}

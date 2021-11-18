<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <style>
        .flex {

            display: flex;
            justify-content: 'space-between';
            margin: 1rem 0;
        }
    </style>
    <?php
    include('config.php');
    $err = '';
    if (isset($_POST['submit'])) {
        $tableName = trim(strip_tags($_POST['table']));
        $action = trim(strip_tags($_POST['action']));

        if (trim(strip_tags($_POST['table'])) == '') {
            $err .= 'choose a table name <br />';
        }
        if (trim(strip_tags($_POST['action'])) == '') {
            $err .= 'choose insert or delete action <br />';
        }
        if ($action == 'inserer') {
            if ($_FILES["csvfile"]["name"] == '') {

                $err .= 'choose a csv file <br />';
            } else {
                $path_parts = pathinfo($_FILES["csvfile"]["name"]);
                $extension = $path_parts['extension'];

                if (strtolower($extension) != 'csv') {

                    $err .= 'choose only a csv file extension <br />';
                }
            }
        }






        if ($err == '') {


            if ($_POST['action'] == "inserer") {
                $fileContent = file($_FILES["csvfile"]["tmp_name"], FILE_USE_INCLUDE_PATH | FILE_IGNORE_NEW_LINES);
                // print_r($fileContent);
                $lineNumber = 1;
                $arrColumns = array();
                $strColumns = '';
                $bindParams = '';
                foreach ($fileContent as $line) {
                    $arrayValues = array();

                    if ($lineNumber == 1) {
                        $arrColumns = explode(';', $line);
                        for ($i = 0; $i < count($arrColumns); $i++) {
                            if ($i == 0 or $i < (count($arrColumns) - 1)) {
                                $strColumns .=   str_replace('"', '', $arrColumns[$i]) . ',';
                                $bindParams .= '?,';
                            } else {
                                $strColumns .=   str_replace('"', '', $arrColumns[$i]);
                                $bindParams .= '?';
                            }
                        }
                    } else {
                        $nameArray = explode(';', $line);
                        for ($i = 0; $i < count($nameArray); $i++) {
                            $var = str_replace('""', '"', $nameArray[$i]);
                            array_push($arrayValues, $var);
                        }

                        $insertQuery = $bdd->prepare("insert into $tableName($strColumns) values($bindParams);");

                        for ($j = 0; $j < count($arrayValues); $j++) {
                            $id = $j + 1;
                            $insertQuery->bindParam($id, $arrayValues[$j]);
                        }
                        $insertQuery->execute();
                    }
                    $lineNumber++;
                }
                // print_r($arrayValues);
                // echo $bindParams;
            } else if ($_POST['action'] == "supprimer") {
                $deleteQuery = $bdd->prepare("delete from $tableName");
                $deleteQuery->execute();
            }
        } else {
            echo $err;
        }
        echo $_POST['table'];
    }

    ?>


    <form action="index.php" method="post" enctype='multipart/form-data'>
        <input type='file' name='csvfile' class="flex" />
        <!-- <select name="db" id="">
            <?php
            // $dbh = new PDO('mysql:host=localhost;user=foo;password=bar;dbname=baz');
            // $statement = $dbh->query('SHOW DATABASES');
            // print_r($statement->fetchAll());

            // while ($ar = $req->fetch()) {
            //     echo '<option value=""></option>';

            // }

            ?>
        </select> -->
        <select name="table" id="table">
            <?php
            $req = $bdd->prepare("SELECT table_name FROM information_schema.tables  WHERE table_schema ='$base';");
            $req->execute();
            while ($ar = $req->fetch()) {
            ?>

                <option value="<?php echo $ar['table_name']; ?>"><?php echo $ar['table_name']; ?></option>
            <?php } ?>
        </select>

        <select name="action" id="action" class="flex">
            <option value="inserer">Inserer</option>
            <option value="supprimer">Supprimer</option>
        </select>

        <button type="submit" name="submit" class="flex">Submit</button>


    </form>



</body>

</html>
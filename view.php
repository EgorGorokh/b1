<!DOCTYPE html>
<html>
<head>
    <title>Просмотр данных</title>
</head>
<body>
    <h2>Данные из файла</h2>
    <?php
    $conn = mysqli_connect("localhost", "root", "root", "b1");

    // Получение идентификатора файла из GET-параметра
    $fileId = $_GET['id'];

    // Запрос для получения имени файла
    $query = "SELECT filename FROM uploaded_files WHERE id = $fileId";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $filename = $row['filename'];

    echo '<h1>'.$filename.'</h1>';


    $filev = pathinfo($filename, PATHINFO_FILENAME);
    $query = "SELECT * FROM $filev";
    $result = mysqli_query($conn, $query);



    if ($result->num_rows > 0) {
        echo "<table><tr>";
        while($row = $result->fetch_assoc()) {
            foreach($row as $key => $value) {
                echo "<th>" . $key . "</th>";
            }
            break;
        }
        echo "</tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach($row as $value) {
                echo "<td>" . $value . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "0 результатов";
    }


   
   
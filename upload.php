<?php

$conn = mysqli_connect("localhost", "root", "root", "b1");

// Проверка наличия загруженного файла
if (isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file'];

    // Проверка на ошибки загрузки файла
    if ($file['error'] === UPLOAD_ERR_OK) {
        $filename = basename($file['name']);

        // Загрузка файла в папку uploads
        $uploadDir = 'uploads/';
        $uploadPath = $uploadDir . $filename;
        move_uploaded_file($file['tmp_name'], $uploadPath);


        // Выбор данных по столбцу filename
        $sql = "SELECT filename FROM uploaded_files";
        $result = $conn->query($sql);

        $found = false;
        $filenames = array();

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $filenames[] = $row["filename"];
            }

            $found = in_array($filename, $filenames);
        }


        if ($found) {
            echo "'da' найдено в столбце filename";
        } else {
            $query = "INSERT INTO uploaded_files (filename) VALUES ('$filename')";
            mysqli_query($conn, $query);

            $filev = pathinfo($filename, PATHINFO_FILENAME);

            // Открытие загруженного CSV-файла
            $file = fopen($uploadPath, "r");
            $headers = fgetcsv($file, 0, ";");

            // Формирование строки для создания таблицы
            $columns = [];
            foreach ($headers as $header) {
                $columns[] = "`$header` VARCHAR(255) NOT NULL";
            }
            $columnsString = implode(", ", $columns);

            // Создание SQL запроса для создания таблицы
            $sqlCreateTable = "CREATE TABLE IF NOT EXISTS $filev ($columnsString)";

            // Выполнение запроса на создание таблицы
            if ($conn->query($sqlCreateTable) === true) {
                echo "Таблица успешно создана";
            } else {
                echo "Ошибка при создании таблицы: " . $conn->error;
            }

            // Подготовка выражения запроса для вставки данных
            $sqlInsertData = "INSERT INTO $filev (" . implode(',', $headers) . ") VALUES (" . rtrim(str_repeat('?,', count($headers)), ',') . ")";

            $stmt = $conn->prepare($sqlInsertData);

            // Чтение данных из CSV файла и сохранение в базу данных
            while (($data = fgetcsv($file, 0, ";")) !== false) {
                $stmt->bind_param(str_repeat('s', count($data)), ...$data);
                $stmt->execute();
            }

            fclose($file);
            echo "Файл успешно загружен и данные сохранены в базе данных.";
        }
    } else {
        echo "Ошибка при загрузке файла.";
    }
}


mysqli_close($conn);
header("location: ./index.php");
exit();

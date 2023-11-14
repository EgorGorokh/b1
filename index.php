<!DOCTYPE html>
<html>
<head>
    <title>Загрузка CSV</title>
</head>
<body>
    <h2>Загрузка CSV</h2>
    <form action="upload.php" method="post" enctype="multipart/form-data">
        <input type="file" name="csv_file" accept=".csv" required>
        <input type="submit" value="Загрузить">
    </form>

    <h2>Список загруженных файлов</h2>
    <ul>
        <?php
        $conn = mysqli_connect("localhost", "root", "root", "b1");

        // Запрос для получения списка загруженных файлов
        $query = "SELECT * FROM uploaded_files";
        $result = mysqli_query($conn, $query);

        // Вывод списка загруженных файлов
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<li><a href='view.php?id=".$row['id']."'>".$row['filename']."</a></li>";
        }

        mysqli_close($conn);
        ?>
    </ul>
</body>
</html>
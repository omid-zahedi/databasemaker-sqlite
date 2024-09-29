<?php
$message = '';

if (isset($_POST['create_db'])) {
    $dbName = $_POST['db_name'];
    $tables = $_POST['tables']; 
    $currentDir = getcwd();
    $dbPath = $currentDir . DIRECTORY_SEPARATOR . $dbName . ".db"; 

    if (empty($dbName) || is_numeric($dbName[0])) {
        $message = "Database name cannot be empty or start with a number.";
    } else {
        try {
            $pdo = new PDO("sqlite:$dbPath");

            foreach ($tables as $table) {
                $tableName = $table['name'];
                
                if (empty($tableName) || is_numeric($tableName[0])) {
                    $message = "Table name cannot be empty or start with a number.";
                    break;
                }

                $query = "CREATE TABLE IF NOT EXISTS `$tableName` (";
                foreach ($table['columns'] as $column) {
                    $columnName = $column['name'];
                    $columnType = $column['type'];

                    if (empty($columnName) || is_numeric($columnName[0])) {
                        $message = "Column name cannot be empty or start with a number.";
                        break 2;
                    }

                    $query .= "`$columnName` $columnType,";
                }
                $query = rtrim($query, ',') . ');'; 

                $pdo->exec($query);
            }

            if (empty($message)) {
                $message = "دیتابیس با موفقیت ایجاد شد : $dbPath";
            }
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> دیتابیس ساز sqlite</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>

<div class="container">
    <h2> ساخت دیتابیس جدید </h2>
    <form method="post">
        <div class="db-section">
            <div class="form-group">
                <label for="db_name">نام دیتابیس:</label>
                <input type="text" id="db_name" name="db_name" required>
            </div>

            <div class="form-group">
                <label for="table_count">تعداد جدول‌ها:</label>
                <input type="number" id="table_count" name="table_count" min="1" max="50" required>
            </div>

            <button type="button" class="create-tables-btn" onclick="generateTables()">ایجاد جدول‌ها</button>
        </div>

        <div id="tables"></div>

        <div class="submit-btn">
            <input type="submit" name="create_db" value="ساخت دیتابیس">
        </div>
    </form>

    <?php if (!empty($message)) : ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>
</div>

<script>
function generateTables() {
    var tableCount = document.getElementById('table_count').value;
    var tablesDiv = document.getElementById('tables');
    tablesDiv.innerHTML = '';

    for (var i = 0; i < tableCount; i++) {
        var tableSection = document.createElement('div');
        tableSection.classList.add('table-section');
        
        tableSection.innerHTML = `
            <div class="form-group">
                <label>نام جدول:</label>
                <input type="text" name="tables[${i}][name]" placeholder="نام جدول را وارد کنید" required>
            </div>
            <div class="form-group">
                <label>تعداد ستون‌های جدول:</label>
                <input type="number" name="tables[${i}][column_count]" min="1" max="50" id="column_count_${i}" required>
            </div>
            <button type="button" onclick="generateColumns(${i})">ایجاد ستون‌ها</button>
            <div class="columns" id="table_columns_${i}" style="margin-top: 20px;"></div>
        `;
        tablesDiv.appendChild(tableSection);
    }
}

function generateColumns(tableIndex) {
    var columnCount = document.getElementById('column_count_' + tableIndex).value;
    var columnsDiv = document.getElementById('table_columns_' + tableIndex);
    columnsDiv.innerHTML = '';

    for (var j = 0; j < columnCount; j++) {
        var columnDiv = document.createElement('div');
        columnDiv.classList.add('column-group');
        columnDiv.innerHTML = `
            <label>ستون ${j + 1}:</label>
            <input type="text" name="tables[${tableIndex}][columns][${j}][name]" placeholder="نام ستون" required>
            <select name="tables[${tableIndex}][columns][${j}][type]" required>
                <option value="TEXT">TEXT</option>
                <option value="INTEGER">INTEGER</option>
                <option value="REAL">REAL</option>
                <option value="BLOB">BLOB</option>
            </select>
        `;
        columnsDiv.appendChild(columnDiv);
    }
}
</script>



<a href="https://t.me/Anony_muos" target="_blank"> طراحی شده توسط امید زاهدی </a>




</body>
</html>

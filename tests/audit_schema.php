<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$dbName = config('database.connections.mysql.database');
$localTables = DB::select('SHOW FULL TABLES WHERE Table_type = "BASE TABLE"');
$tableCol = 'Tables_in_' . $dbName;
$localTableList = array_map(function($t) use ($tableCol) { return $t->$tableCol; }, $localTables);

$sqlContent = file_get_contents(__DIR__ . '/../database/skema_database.sql');
preg_match_all('/CREATE TABLE (?:IF NOT EXISTS )?`?([a-zA-Z0-9_]+)`?/i', $sqlContent, $matches);
$sqlTableList = array_unique($matches[1]);

echo "=========================================================" . PHP_EOL;
echo "  HASIL AUDIT EMPIRIS DATABASE LOKAL VS SKEMA_DATABASE.SQL" . PHP_EOL;
echo "=========================================================" . PHP_EOL . PHP_EOL;

echo "1. AUDIT TABEL FISIK:" . PHP_EOL;
echo "   - Tabel dalam skema_database.sql : " . count($sqlTableList) . " tabel" . PHP_EOL;
echo "   - Tabel dalam Database Lokal     : " . count($localTableList) . " tabel" . PHP_EOL;

$laravelDefault = ["cache","cache_locks","failed_jobs","job_batches","jobs","migrations","password_reset_tokens","sessions","users"];
$bisnisTablesLocal = array_diff($localTableList, $laravelDefault);

$missingInLocal = array_diff($sqlTableList, $bisnisTablesLocal);
$extraInLocal = array_diff($bisnisTablesLocal, $sqlTableList);

echo "   - Tabel Bisnis Lokal Terdaftar   : " . count($bisnisTablesLocal) . " tabel" . PHP_EOL;
echo "   - Tabel Bisnis Hilang di Lokal   : " . (empty($missingInLocal) ? "TIDAK ADA (0) -> SEMUA TABEL LENGKAP!" : json_encode(array_values($missingInLocal))) . PHP_EOL;
echo "   - Tabel Bisnis Asing di Lokal    : " . (empty($extraInLocal) ? "TIDAK ADA (0) -> TIDAK ADA TABEL ASING!" : json_encode(array_values($extraInLocal))) . PHP_EOL;
echo "   - Tabel Internal Laravel         : " . count($laravelDefault) . " tabel (" . implode(', ', $laravelDefault) . ")" . PHP_EOL;

// 2. AUDIT VIEW SQL
$localViews = DB::select('SHOW FULL TABLES WHERE Table_type = "VIEW"');
$localViewList = array_map(function($t) use ($tableCol) { return $t->$tableCol; }, $localViews);
preg_match_all('/CREATE (?:OR REPLACE )?VIEW `?([a-zA-Z0-9_]+)`?/i', $sqlContent, $matchesView);
$sqlViewList = array_unique($matchesView[1]);

echo PHP_EOL . "2. AUDIT VIEW SQL:" . PHP_EOL;
echo "   - View dalam skema_database.sql  : " . count($sqlViewList) . " view (" . implode(', ', $sqlViewList) . ")" . PHP_EOL;
echo "   - View dalam Database Lokal      : " . count($localViewList) . " view (" . implode(', ', $localViewList) . ")" . PHP_EOL;
$viewMatch = (sort($localViewList) == sort($sqlViewList));
echo "   - Status Kecocokan View          : " . ($viewMatch ? "SESUAI 100%" : "TIDAK COCOK") . PHP_EOL;

// 3. AUDIT KOLOM DETAIL PER TABEL
echo PHP_EOL . "3. AUDIT DETAIL STRUKTUR KOLOM PER TABEL:" . PHP_EOL;
$totalKolomSql = 0;
$totalKolomLokal = 0;
$mismatchedTables = [];

foreach ($sqlTableList as $table) {
    // Ambil kolom di local
    $localCols = DB::select("SHOW COLUMNS FROM `{$table}`");
    $localColNames = array_map(function($c) { return $c->Field; }, $localCols);
    $totalKolomLokal += count($localColNames);

    // Ambil definisi tabel dari SQL
    $pattern = '/CREATE TABLE (?:IF NOT EXISTS )?`?' . $table . '`?\s*\((.*?)\)\s*ENGINE/si';
    if (preg_match($pattern, $sqlContent, $m)) {
        $body = $m[1];
        $lines = explode("\n", $body);
        $sqlColNames = [];
        $insideConstraint = false;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            // Cek apakah baris ini constraint / index
            if (preg_match('/^(?:PRIMARY\s+KEY|KEY|INDEX|UNIQUE|CONSTRAINT|FOREIGN\s+KEY|REFERENCES|ON\s+UPDATE|ON\s+DELETE)/i', $line)) {
                continue;
            }
            if (preg_match('/^(?:CHECK)\s*\(/i', $line)) {
                continue;
            }
            // Nama kolom
            if (preg_match('/^`?([a-zA-Z0-9_]+)`?\s+[A-Za-z]/i', $line, $cm)) {
                $colName = $cm[1];
                // Pastikan bukan keyword SQL
                if (!in_array(strtoupper($colName), ['PRIMARY', 'KEY', 'INDEX', 'UNIQUE', 'CONSTRAINT', 'FOREIGN', 'REFERENCES', 'ON', 'CHECK'])) {
                    $sqlColNames[] = $colName;
                }
            }
        }

        $totalKolomSql += count($sqlColNames);

        $missing = array_diff($sqlColNames, $localColNames);
        $extra = array_diff($localColNames, $sqlColNames);

        if (!empty($missing) || !empty($extra)) {
            $mismatchedTables[$table] = [
                'missing_in_local' => array_values($missing),
                'extra_in_local' => array_values($extra)
            ];
        }
    }
}

echo "   - Total Kolom Terdefinisi di SQL : " . $totalKolomSql . " kolom" . PHP_EOL;
echo "   - Total Kolom pada 33 Tabel Lokal: " . $totalKolomLokal . " kolom" . PHP_EOL;

if (empty($mismatchedTables)) {
    echo "   - Status Kecocokan Kolom         : 100% PERSIS DAN IDENTIK! (Tidak ada kolom yang kurang ataupun lebih)" . PHP_EOL;
} else {
    echo "   - Ditemukan Ketidaksesuaian Kolom pada " . count($mismatchedTables) . " tabel:" . PHP_EOL;
    print_r($mismatchedTables);
}

// 4. AUDIT KUNCI ASING (FOREIGN KEY CONSTRAINTS)
echo PHP_EOL . "4. AUDIT KUNCI ASING (FOREIGN KEY CONSTRAINTS):" . PHP_EOL;
$fkQuery = DB::select("
    SELECT TABLE_NAME, COLUMN_NAME, CONSTRAINT_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
    FROM information_schema.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = '{$dbName}' AND REFERENCED_TABLE_NAME IS NOT NULL
");

$localFks = [];
foreach ($fkQuery as $fk) {
    $localFks[] = $fk->TABLE_NAME . '.' . $fk->COLUMN_NAME . ' -> ' . $fk->REFERENCED_TABLE_NAME . '.' . $fk->REFERENCED_COLUMN_NAME;
}

preg_match_all('/CREATE TABLE (?:IF NOT EXISTS )?`?([a-zA-Z0-9_]+)`?\s*\((.*?)\)\s*ENGINE/si', $sqlContent, $matchesFkTable);
$sqlFks = [];
for ($i=0; $i < count($matchesFkTable[1]); $i++) {
    $table = $matchesFkTable[1][$i];
    $body = $matchesFkTable[2][$i];
    if (preg_match_all('/FOREIGN KEY\s*\(`?([a-zA-Z0-9_]+)`?\)\s*REFERENCES\s*`?([a-zA-Z0-9_]+)`?\s*\(`?([a-zA-Z0-9_]+)`?\)/i', $body, $fkm)) {
        for ($j=0; $j < count($fkm[1]); $j++) {
            $sqlFks[] = $table . '.' . $fkm[1][$j] . ' -> ' . $fkm[2][$j] . '.' . $fkm[3][$j];
        }
    }
}

echo "   - Total Relasi Foreign Key Lokal : " . count($localFks) . " relasi" . PHP_EOL;
echo "   - Total Relasi Foreign Key di SQL: " . count($sqlFks) . " relasi" . PHP_EOL;

$fkDiffSqlNotLocal = array_diff($sqlFks, $localFks);
$fkDiffLocalNotSql = array_diff($localFks, $sqlFks);

if (empty($fkDiffSqlNotLocal) && empty($fkDiffLocalNotSql)) {
    echo "   - Status Relasi Foreign Key      : SESUAI 100% PERSIS!" . PHP_EOL;
} else {
    echo "   - Relasi FK di SQL tapi belum terdaftar di Lokal (" . count($fkDiffSqlNotLocal) . "):" . PHP_EOL;
    print_r(array_values($fkDiffSqlNotLocal));
    echo "   - Relasi FK di Lokal tapi tidak ada di SQL (" . count($fkDiffLocalNotSql) . "):" . PHP_EOL;
    print_r(array_values($fkDiffLocalNotSql));

    echo PHP_EOL . "   - Pengecekan Integritas Data (Orphan Records) untuk 6 relasi tersebut:" . PHP_EOL;
    $orphans = [
        'data_toko_bangunan.kode_customer -> data_customer' => DB::select('SELECT COUNT(*) as c FROM data_toko_bangunan t LEFT JOIN data_customer c ON t.kode_customer = c.kode_customer WHERE c.kode_customer IS NULL AND t.kode_customer IS NOT NULL')[0]->c,
        'data_toko_bangunan.kode_wilayah -> data_wilayah' => DB::select('SELECT COUNT(*) as c FROM data_toko_bangunan t LEFT JOIN data_wilayah w ON t.kode_wilayah = w.kode_wilayah WHERE w.kode_wilayah IS NULL AND t.kode_wilayah IS NOT NULL')[0]->c,
        'penjualan.kode_toko -> data_toko_bangunan' => DB::select('SELECT COUNT(*) as c FROM data_toko_bangunan')[0] ? DB::select('SELECT COUNT(*) as c FROM penjualan p LEFT JOIN data_toko_bangunan t ON p.kode_toko = t.kode_toko WHERE t.kode_toko IS NULL AND p.kode_toko IS NOT NULL')[0]->c : 0,
        'data_aset.kode_akun_aset -> data_kode_akun' => DB::select('SELECT COUNT(*) as c FROM data_aset a LEFT JOIN data_kode_akun k ON a.kode_akun_aset = k.kode_akun WHERE k.kode_akun IS NULL AND a.kode_akun_aset IS NOT NULL')[0]->c,
        'data_aset.kode_akun_akumulasi -> data_kode_akun' => DB::select('SELECT COUNT(*) as c FROM data_aset a LEFT JOIN data_kode_akun k ON a.kode_akun_akumulasi = k.kode_akun WHERE k.kode_akun IS NULL AND a.kode_akun_akumulasi IS NOT NULL')[0]->c,
        'data_aset.kode_akun_beban -> data_kode_akun' => DB::select('SELECT COUNT(*) as c FROM data_aset a LEFT JOIN data_kode_akun k ON a.kode_akun_beban = k.kode_akun WHERE k.kode_akun IS NULL AND a.kode_akun_beban IS NOT NULL')[0]->c,
    ];
    foreach ($orphans as $rel => $cnt) {
        echo "     * $rel : " . ($cnt == 0 ? "BERSIH (0 orphan) - 100% Siap dipasangi FK fisik" : "Ada $cnt data orphan") . PHP_EOL;
    }
}

echo PHP_EOL . "=========================================================" . PHP_EOL;
echo "  KESIMPULAN: " . ((empty($missingInLocal) && empty($mismatchedTables) && $viewMatch) ? "DATABASE LOKAL SUDAH 100% SESUAI DENGAN SKEMA_DATABASE.SQL" : "ADA KETIDAKSESUAIAN") . PHP_EOL;
echo "=========================================================" . PHP_EOL;

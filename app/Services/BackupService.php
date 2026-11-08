<?php

namespace App\Services;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class BackupService
{
    protected string $backupDir;

    public function __construct()
    {
        $this->backupDir = storage_path('app/backups');

        if (! File::exists($this->backupDir)) {
            File::makeDirectory($this->backupDir, 0755, true);
        }
    }

    /**
     * Get backup directory path.
     */
    public function getBackupDir(): string
    {
        return $this->backupDir;
    }

    /**
     * Get overview metrics about the database and backup storage.
     */
    public function getDatabaseStats(): array
    {
        $dbName = config('database.connections.mysql.database', 'ashahi');

        try {
            $tables = DB::select("
                SELECT 
                    COUNT(*) as table_count,
                    COALESCE(SUM(data_length + index_length), 0) as size_bytes
                FROM information_schema.tables 
                WHERE table_schema = ?
            ", [$dbName]);

            $versionResult = DB::select('SELECT VERSION() as version');
            $mysqlVersion = $versionResult[0]->version ?? 'Unknown';

            $tableCount = $tables[0]->table_count ?? 0;
            $sizeBytes = (int) ($tables[0]->size_bytes ?? 0);

            return [
                'database' => $dbName,
                'version' => $mysqlVersion,
                'table_count' => (int) $tableCount,
                'size_bytes' => $sizeBytes,
                'size_formatted' => $this->formatBytes($sizeBytes),
            ];
        } catch (\Throwable $e) {
            Log::warning('BackupService: failed to fetch DB stats', ['error' => $e->getMessage()]);

            return [
                'database' => $dbName,
                'version' => 'MySQL',
                'table_count' => 0,
                'size_bytes' => 0,
                'size_formatted' => '0 B',
            ];
        }
    }

    /**
     * List all existing backup files in storage/app/backups.
     */
    public function listBackups(): array
    {
        if (! File::exists($this->backupDir)) {
            return [];
        }

        $files = File::files($this->backupDir);
        $backups = [];

        foreach ($files as $file) {
            $name = $file->getFilename();

            // Skip hidden or temporary files
            if (str_starts_with($name, '.')) {
                continue;
            }

            $size = $file->getSize();
            $mtime = $file->getMTime();
            $ext = strtolower($file->getExtension());

            $type = 'database';
            if (str_contains($name, 'full-backup')) {
                $type = 'full';
            } elseif (str_contains($name, 'files-backup')) {
                $type = 'files';
            }

            $backups[] = [
                'filename' => $name,
                'path' => $file->getPathname(),
                'size' => $size,
                'size_formatted' => $this->formatBytes($size),
                'type' => $type,
                'extension' => $ext,
                'created_at' => $mtime,
                'created_at_formatted' => date('Y-m-d H:i:s', $mtime),
                'age' => $this->humanTimeDiff($mtime),
                'can_restore' => in_array($ext, ['sql', 'gz', 'zip']),
            ];
        }

        // Sort descending by created_at (newest first)
        usort($backups, fn ($a, $b) => $b['created_at'] <=> $a['created_at']);

        return $backups;
    }

    /**
     * Create a database backup.
     * Generates .sql or .sql.gz file.
     */
    public function createDatabaseBackup(bool $compress = true): array
    {
        $timestamp = date('Y-m-d_H-i-s');
        $baseName = "db-backup-{$timestamp}.sql";
        $sqlPath = $this->backupDir.DIRECTORY_SEPARATOR.$baseName;

        $dumped = $this->dumpDatabase($sqlPath);

        if (! $dumped || ! File::exists($sqlPath)) {
            throw new Exception('Failed to generate database dump.');
        }

        if ($compress && function_exists('gzencode')) {
            $gzPath = $sqlPath.'.gz';
            $sqlContent = File::get($sqlPath);
            $gzData = gzencode($sqlContent, 9);

            if ($gzData !== false) {
                File::put($gzPath, $gzData);
                File::delete($sqlPath);
                $finalPath = $gzPath;
                $finalName = basename($gzPath);
            } else {
                $finalPath = $sqlPath;
                $finalName = $baseName;
            }
        } else {
            $finalPath = $sqlPath;
            $finalName = $baseName;
        }

        $size = File::size($finalPath);

        return [
            'success' => true,
            'filename' => $finalName,
            'path' => $finalPath,
            'size' => $size,
            'size_formatted' => $this->formatBytes($size),
            'type' => 'database',
        ];
    }

    /**
     * Create a backup of user uploads and storage files.
     * Backs up storage/app/public and public/uploads into a .zip.
     */
    public function createFilesBackup(): array
    {
        if (! class_exists(ZipArchive::class)) {
            throw new Exception('PHP ZipArchive extension is required for file backups.');
        }

        $timestamp = date('Y-m-d_H-i-s');
        $zipName = "files-backup-{$timestamp}.zip";
        $zipPath = $this->backupDir.DIRECTORY_SEPARATOR.$zipName;

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new Exception("Cannot create zip file at {$zipPath}");
        }

        $directoriesToBackup = [
            'storage/app/public' => storage_path('app/public'),
            'public/uploads' => public_path('uploads'),
            'public/avatars' => public_path('avatars'),
            'public/categories' => public_path('categories'),
            'public/products' => public_path('products'),
            'public/settings' => public_path('settings'),
        ];

        $filesCount = 0;
        foreach ($directoriesToBackup as $localPrefix => $dir) {
            if (File::isDirectory($dir)) {
                $filesCount += $this->addFolderToZip($zip, $dir, $localPrefix);
            }
        }

        $zip->close();

        if ($filesCount === 0 && (! File::exists($zipPath) || File::size($zipPath) < 100)) {
            // Even if empty, leave a valid archive
        }

        $size = File::exists($zipPath) ? File::size($zipPath) : 0;

        return [
            'success' => true,
            'filename' => $zipName,
            'path' => $zipPath,
            'size' => $size,
            'size_formatted' => $this->formatBytes($size),
            'type' => 'files',
            'files_count' => $filesCount,
        ];
    }

    /**
     * Create a full backup: Database dump + Uploaded files in a single .zip.
     */
    public function createFullBackup(): array
    {
        if (! class_exists(ZipArchive::class)) {
            throw new Exception('PHP ZipArchive extension is required for full backups.');
        }

        $timestamp = date('Y-m-d_H-i-s');
        $zipName = "full-backup-{$timestamp}.zip";
        $zipPath = $this->backupDir.DIRECTORY_SEPARATOR.$zipName;

        // 1. Dump database to a temporary .sql file
        $tempSql = $this->backupDir.DIRECTORY_SEPARATOR."temp_db_{$timestamp}.sql";
        $this->dumpDatabase($tempSql);

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            if (File::exists($tempSql)) {
                File::delete($tempSql);
            }
            throw new Exception("Cannot create zip file at {$zipPath}");
        }

        // Add database dump
        if (File::exists($tempSql)) {
            $zip->addFile($tempSql, 'database.sql');
        }

        // Add uploaded files
        $directories = [
            'storage/app/public' => storage_path('app/public'),
            'public/uploads' => public_path('uploads'),
            'public/avatars' => public_path('avatars'),
            'public/categories' => public_path('categories'),
            'public/products' => public_path('products'),
            'public/settings' => public_path('settings'),
        ];

        foreach ($directories as $localPrefix => $dir) {
            if (File::isDirectory($dir)) {
                $this->addFolderToZip($zip, $dir, $localPrefix);
            }
        }

        // Add a manifest metadata file
        $manifest = [
            'app_name' => config('app.name'),
            'created_at' => date('Y-m-d H:i:s'),
            'backup_type' => 'full',
            'database' => config('database.connections.mysql.database'),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
        ];
        $zip->addFromString('manifest.json', json_encode($manifest, JSON_PRETTY_PRINT));

        $zip->close();

        // Clean up temp SQL
        if (File::exists($tempSql)) {
            File::delete($tempSql);
        }

        $size = File::exists($zipPath) ? File::size($zipPath) : 0;

        return [
            'success' => true,
            'filename' => $zipName,
            'path' => $zipPath,
            'size' => $size,
            'size_formatted' => $this->formatBytes($size),
            'type' => 'full',
        ];
    }

    /**
     * Download a backup file securely.
     */
    public function downloadBackup(string $filename): BinaryFileResponse
    {
        $safeName = basename($filename);
        $path = $this->backupDir.DIRECTORY_SEPARATOR.$safeName;

        if (! File::exists($path)) {
            abort(404, 'Backup file not found.');
        }

        return response()->download($path, $safeName, [
            'Content-Type' => 'application/octet-stream',
        ]);
    }

    /**
     * Delete a backup file.
     */
    public function deleteBackup(string $filename): bool
    {
        $safeName = basename($filename);
        $path = $this->backupDir.DIRECTORY_SEPARATOR.$safeName;

        if (File::exists($path)) {
            return File::delete($path);
        }

        return false;
    }

    /**
     * Store an uploaded backup file.
     */
    public function storeUploadedBackup(UploadedFile $file): array
    {
        $origName = $file->getClientOriginalName();
        $safeBase = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $origName);

        // Ensure safe extension
        $ext = strtolower($file->getClientOriginalExtension());
        if (! in_array($ext, ['sql', 'gz', 'zip'])) {
            throw new Exception('Invalid backup file type. Only .sql, .sql.gz, and .zip files are supported.');
        }

        $targetName = 'uploaded_'.date('Y-m-d_H-i-s').'_'.$safeBase;
        $file->move($this->backupDir, $targetName);

        $path = $this->backupDir.DIRECTORY_SEPARATOR.$targetName;
        $size = File::size($path);

        return [
            'success' => true,
            'filename' => $targetName,
            'path' => $path,
            'size' => $size,
            'size_formatted' => $this->formatBytes($size),
        ];
    }

    /**
     * Restore database from a backup file.
     */
    public function restoreBackup(string $filename): array
    {
        $safeName = basename($filename);
        $path = $this->backupDir.DIRECTORY_SEPARATOR.$safeName;

        if (! File::exists($path)) {
            throw new Exception('Backup file does not exist.');
        }

        $sqlPath = $path;
        $tempExtractDir = null;

        if (str_ends_with($safeName, '.zip')) {
            // Extract database.sql from zip
            $zip = new ZipArchive();
            if ($zip->open($path) !== true) {
                throw new Exception('Failed to open zip backup archive.');
            }

            $tempExtractDir = $this->backupDir.DIRECTORY_SEPARATOR.'temp_restore_'.uniqid();
            File::makeDirectory($tempExtractDir, 0755, true);

            $hasDb = false;
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $entry = $zip->getNameIndex($i);
                if ($entry === 'database.sql' || str_ends_with($entry, '.sql')) {
                    $zip->extractTo($tempExtractDir, $entry);
                    $sqlPath = $tempExtractDir.DIRECTORY_SEPARATOR.$entry;
                    $hasDb = true;
                    break;
                }
            }

            $zip->close();

            if (! $hasDb) {
                File::deleteDirectory($tempExtractDir);
                throw new Exception('The selected zip file does not contain a database.sql file to restore.');
            }
        } elseif (str_ends_with($safeName, '.gz')) {
            $tempSql = $this->backupDir.DIRECTORY_SEPARATOR.'temp_decompressed_'.uniqid().'.sql';
            $gzData = File::get($path);
            $decompressed = gzdecode($gzData);

            if ($decompressed === false) {
                throw new Exception('Failed to decompress .gz backup file.');
            }

            File::put($tempSql, $decompressed);
            $sqlPath = $tempSql;
        }

        // Execute SQL restore
        try {
            $this->executeSqlRestore($sqlPath);
            $success = true;
            $error = null;
        } catch (\Throwable $e) {
            $success = false;
            $error = $e->getMessage();
            Log::error('BackupService: restore failed', ['error' => $error]);
        } finally {
            // Clean up temporary files
            if ($tempExtractDir && File::isDirectory($tempExtractDir)) {
                File::deleteDirectory($tempExtractDir);
            }
            if (isset($tempSql) && File::exists($tempSql)) {
                File::delete($tempSql);
            }
        }

        if (! $success) {
            throw new Exception("Restore failed: {$error}");
        }

        return [
            'success' => true,
            'message' => "Database successfully restored from {$safeName}.",
        ];
    }

    /**
     * Dump database using mysqldump if available, or PHP PDO fallback.
     */
    protected function dumpDatabase(string $destinationPath): bool
    {
        $dbConfig = config('database.connections.mysql');
        $host = $dbConfig['host'] ?? '127.0.0.1';
        $port = $dbConfig['port'] ?? '3306';
        $database = $dbConfig['database'] ?? '';
        $username = $dbConfig['username'] ?? 'root';
        $password = $dbConfig['password'] ?? '';

        // Check for compatible mysqldump binary
        $dumpBinary = $this->findMysqldumpBinary();

        if ($dumpBinary) {
            $passParam = $password !== '' ? '-p'.escapeshellarg($password) : '';
            $cmd = sprintf(
                '%s --host=%s --port=%s --user=%s %s --single-transaction --quick --skip-lock-tables %s > %s 2>&1',
                escapeshellcmd($dumpBinary),
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($username),
                $passParam,
                escapeshellarg($database),
                escapeshellarg($destinationPath)
            );

            exec($cmd, $output, $returnVar);

            if ($returnVar === 0 && File::exists($destinationPath) && File::size($destinationPath) > 500) {
                return true;
            }

            Log::warning('mysqldump binary failed, switching to PHP PDO dump', [
                'exit_code' => $returnVar,
                'output' => implode("\n", $output),
            ]);
        }

        // Pure PHP fallback
        return $this->dumpDatabaseViaPhp($destinationPath);
    }

    /**
     * Find an available and functional mysqldump executable.
     */
    protected function findMysqldumpBinary(): ?string
    {
        $candidates = [
            '/Applications/XAMPP/xamppfiles/bin/mysqldump',
            '/usr/local/bin/mysqldump',
            '/usr/bin/mysqldump',
            '/opt/homebrew/bin/mysqldump',
        ];

        foreach ($candidates as $path) {
            if (is_executable($path)) {
                // Verify it doesn't fail immediately with missing auth plugin
                $test = exec(escapeshellcmd($path).' --version 2>&1', $out, $ret);
                if ($ret === 0) {
                    return $path;
                }
            }
        }

        return null;
    }

    /**
     * Fallback pure PHP database dumper using PDO.
     */
    protected function dumpDatabaseViaPhp(string $destinationPath): bool
    {
        $pdo = DB::connection()->getPdo();
        $dbName = config('database.connections.mysql.database');

        $fp = fopen($destinationPath, 'w');
        if (! $fp) {
            return false;
        }

        fwrite($fp, "-- Ashahi Database Backup (PHP Fallback)\n");
        fwrite($fp, "-- Generated: ".date('Y-m-d H:i:s')."\n");
        fwrite($fp, "-- Database: `{$dbName}`\n\n");
        fwrite($fp, "SET FOREIGN_KEY_CHECKS=0;\n");
        fwrite($fp, "SET SQL_MODE=\"NO_AUTO_VALUE_ON_ZERO\";\n");
        fwrite($fp, "SET NAMES utf8mb4;\n\n");

        $tablesStmt = $pdo->query('SHOW TABLES');
        $tables = $tablesStmt->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            // Table structure
            fwrite($fp, "-- --------------------------------------------------------\n");
            fwrite($fp, "-- Structure for table `{$table}`\n");
            fwrite($fp, "-- --------------------------------------------------------\n\n");
            fwrite($fp, "DROP TABLE IF EXISTS `{$table}`;\n");

            $createStmt = $pdo->query("SHOW CREATE TABLE `{$table}`");
            $createRow = $createStmt->fetch(\PDO::FETCH_ASSOC);
            $createSql = $createRow['Create Table'] ?? null;

            if ($createSql) {
                fwrite($fp, $createSql.";\n\n");
            }

            // Table data
            fwrite($fp, "-- Data for table `{$table}`\n\n");

            $dataStmt = $pdo->query("SELECT * FROM `{$table}`");
            $batch = [];
            $batchSize = 100;

            while ($row = $dataStmt->fetch(\PDO::FETCH_ASSOC)) {
                $values = [];
                foreach ($row as $val) {
                    if ($val === null) {
                        $values[] = 'NULL';
                    } elseif (is_numeric($val) && ! str_starts_with((string) $val, '0')) {
                        $values[] = $val;
                    } else {
                        $values[] = $pdo->quote($val);
                    }
                }
                $batch[] = '('.implode(', ', $values).')';

                if (count($batch) >= $batchSize) {
                    fwrite($fp, "INSERT INTO `{$table}` VALUES \n".implode(",\n", $batch).";\n");
                    $batch = [];
                }
            }

            if (! empty($batch)) {
                fwrite($fp, "INSERT INTO `{$table}` VALUES \n".implode(",\n", $batch).";\n");
            }

            fwrite($fp, "\n");
        }

        fwrite($fp, "SET FOREIGN_KEY_CHECKS=1;\n");
        fclose($fp);

        return true;
    }

    /**
     * Execute SQL file restoration directly into the MySQL database.
     */
    protected function executeSqlRestore(string $sqlFilePath): void
    {
        $dbConfig = config('database.connections.mysql');
        $host = $dbConfig['host'] ?? '127.0.0.1';
        $port = $dbConfig['port'] ?? '3306';
        $database = $dbConfig['database'] ?? '';
        $username = $dbConfig['username'] ?? 'root';
        $password = $dbConfig['password'] ?? '';

        // Try CLI mysql binary first
        $mysqlBinaries = [
            '/Applications/XAMPP/xamppfiles/bin/mysql',
            '/usr/local/bin/mysql',
            '/usr/bin/mysql',
            '/opt/homebrew/bin/mysql',
        ];

        $mysqlBinary = null;
        foreach ($mysqlBinaries as $bin) {
            if (is_executable($bin)) {
                $mysqlBinary = $bin;
                break;
            }
        }

        if ($mysqlBinary) {
            $passParam = $password !== '' ? '-p'.escapeshellarg($password) : '';
            $cmd = sprintf(
                '%s --host=%s --port=%s --user=%s %s %s < %s 2>&1',
                escapeshellcmd($mysqlBinary),
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($username),
                $passParam,
                escapeshellarg($database),
                escapeshellarg($sqlFilePath)
            );

            exec($cmd, $output, $returnVar);

            if ($returnVar === 0) {
                return;
            }

            Log::warning('CLI mysql restore failed, falling back to PDO executor', [
                'exit_code' => $returnVar,
                'output' => implode("\n", $output),
            ]);
        }

        // Fallback PDO execution
        $pdo = DB::connection()->getPdo();
        $pdo->exec('SET FOREIGN_KEY_CHECKS=0;');

        $handle = fopen($sqlFilePath, 'r');
        if (! $handle) {
            throw new Exception("Cannot read SQL file {$sqlFilePath}");
        }

        $query = '';
        while (($line = fgets($handle)) !== false) {
            $trimmed = trim($line);
            // Ignore empty lines and comments
            if ($trimmed === '' || str_starts_with($trimmed, '--') || str_starts_with($trimmed, '/*')) {
                continue;
            }

            $query .= $line;

            if (str_ends_with($trimmed, ';')) {
                try {
                    $pdo->exec($query);
                } catch (\Throwable $e) {
                    // Log and continue on non-critical warning
                    Log::debug('SQL Restore query notice', ['error' => $e->getMessage()]);
                }
                $query = '';
            }
        }

        fclose($handle);
        $pdo->exec('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Recursively add a folder to ZipArchive.
     */
    protected function addFolderToZip(ZipArchive $zip, string $dir, string $localPrefix): int
    {
        $count = 0;
        $files = File::allFiles($dir);

        foreach ($files as $file) {
            $relPath = $file->getRelativePathname();
            $zipPath = $localPrefix.DIRECTORY_SEPARATOR.$relPath;
            $zip->addFile($file->getPathname(), $zipPath);
            $count++;
        }

        return $count;
    }

    /**
     * Format bytes into human readable format.
     */
    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision).' '.$units[$pow];
    }

    /**
     * Human readable time diff.
     */
    protected function humanTimeDiff(int $timestamp): string
    {
        $diff = time() - $timestamp;

        if ($diff < 60) {
            return 'Just now';
        }
        if ($diff < 3600) {
            $mins = floor($diff / 60);

            return $mins.' '.($mins === 1 ? 'min' : 'mins').' ago';
        }
        if ($diff < 86400) {
            $hours = floor($diff / 3600);

            return $hours.' '.($hours === 1 ? 'hour' : 'hours').' ago';
        }

        $days = floor($diff / 86400);

        return $days.' '.($days === 1 ? 'day' : 'days').' ago';
    }
}

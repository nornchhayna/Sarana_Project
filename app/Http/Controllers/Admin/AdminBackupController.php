<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BackupService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AdminBackupController extends Controller
{
    public function __construct(
        protected BackupService $backupService
    ) {}

    /**
     * Display a listing of backups, storage stats, and database information.
     */
    public function index()
    {
        $dbStats = $this->backupService->getDatabaseStats();
        $backups = $this->backupService->listBackups();

        $totalBytes = array_sum(array_column($backups, 'size'));
        $totalFormatted = $this->formatBytes($totalBytes);
        $latestBackup = $backups[0] ?? null;

        return view('admin.backups.index', compact(
            'dbStats',
            'backups',
            'totalBytes',
            'totalFormatted',
            'latestBackup'
        ));
    }

    /**
     * Create a new backup on demand (database, files, or full).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:database,files,full',
            'compress' => 'nullable|boolean',
        ]);

        $type = $validated['type'];
        $compress = $request->boolean('compress', true);

        try {
            switch ($type) {
                case 'files':
                    $result = $this->backupService->createFilesBackup();
                    $label = "Media & uploads backup created: {$result['filename']} ({$result['size_formatted']})";
                    break;

                case 'full':
                    $result = $this->backupService->createFullBackup();
                    $label = "Full backup (Database + Files) created: {$result['filename']} ({$result['size_formatted']})";
                    break;

                case 'database':
                default:
                    $result = $this->backupService->createDatabaseBackup($compress);
                    $label = "Database backup created: {$result['filename']} ({$result['size_formatted']})";
                    break;
            }

            return redirect()
                ->route('admin.backups.index')
                ->with('success', $label);

        } catch (Exception $e) {
            return redirect()
                ->route('admin.backups.index')
                ->with('error', 'Backup failed: '.$e->getMessage());
        }
    }

    /**
     * Download a specific backup file.
     */
    public function download(string $filename): BinaryFileResponse
    {
        return $this->backupService->downloadBackup($filename);
    }

    /**
     * Restore database from a chosen backup file.
     */
    public function restore(Request $request, string $filename): RedirectResponse
    {
        $request->validate([
            'confirm_restore' => 'required|accepted',
        ], [
            'confirm_restore.accepted' => 'You must check the confirmation box to restore the database.',
        ]);

        try {
            $result = $this->backupService->restoreBackup($filename);

            return redirect()
                ->route('admin.backups.index')
                ->with('success', $result['message']);

        } catch (Exception $e) {
            return redirect()
                ->route('admin.backups.index')
                ->with('error', 'Restore failed: '.$e->getMessage());
        }
    }

    /**
     * Delete a backup file.
     */
    public function destroy(string $filename): RedirectResponse
    {
        $deleted = $this->backupService->deleteBackup($filename);

        if ($deleted) {
            return redirect()
                ->route('admin.backups.index')
                ->with('success', "Backup \"{$filename}\" was successfully deleted.");
        }

        return redirect()
                ->route('admin.backups.index')
                ->with('error', "Backup \"{$filename}\" could not be deleted.");
    }

    /**
     * Upload an existing backup file to the backup directory.
     */
    public function upload(Request $request): RedirectResponse
    {
        $request->validate([
            'backup_file' => 'required|file|max:524288', // 512 MB
        ], [
            'backup_file.required' => 'Please select a backup file to upload.',
            'backup_file.max' => 'The backup file must not exceed 512MB.',
        ]);

        try {
            $result = $this->backupService->storeUploadedBackup($request->file('backup_file'));

            return redirect()
                ->route('admin.backups.index')
                ->with('success', "Backup uploaded successfully: {$result['filename']} ({$result['size_formatted']})");

        } catch (Exception $e) {
            return redirect()
                ->route('admin.backups.index')
                ->with('error', 'Upload failed: '.$e->getMessage());
        }
    }

    /**
     * Helper to format bytes.
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
}

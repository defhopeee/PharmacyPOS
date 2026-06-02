<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Symfony\Component\Process\Process;

class BackupController extends Controller
{
    /**
     * Stream a fresh SQL dump of the database to the owner for download.
     */
    public function download()
    {
        $db = config('database.connections.mysql');
        $filename = 'pharmacypos-backup-'.Carbon::now()->format('Ymd-His').'.sql';

        $dump = $this->locateMysqldump();

        if ($dump) {
            $command = [
                $dump,
                '--host='.$db['host'],
                '--port='.$db['port'],
                '--user='.$db['username'],
            ];
            if (! empty($db['password'])) {
                $command[] = '--password='.$db['password'];
            }
            $command[] = '--single-transaction';
            $command[] = '--skip-lock-tables';
            $command[] = $db['database'];

            $process = new Process($command);
            $process->setTimeout(120);
            $process->run();

            if ($process->isSuccessful()) {
                return response($process->getOutput(), 200, [
                    'Content-Type' => 'application/sql',
                    'Content-Disposition' => 'attachment; filename="'.$filename.'"',
                ]);
            }
        }

        // Fallback: build the dump with PHP if mysqldump is unavailable.
        return response($this->phpDump($db), 200, [
            'Content-Type' => 'application/sql',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    private function locateMysqldump(): ?string
    {
        foreach (['C:\\xampp\\mysql\\bin\\mysqldump.exe', '/usr/bin/mysqldump', '/usr/local/bin/mysqldump', 'mysqldump'] as $path) {
            if ($path === 'mysqldump' || is_file($path)) {
                return $path;
            }
        }

        return null;
    }

    private function phpDump(array $db): string
    {
        $pdo = \DB::connection()->getPdo();
        $sql = "-- PharmacyPOS database backup\n-- Generated: ".Carbon::now()->toDateTimeString()."\n\nSET FOREIGN_KEY_CHECKS=0;\n\n";

        $tables = $pdo->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            $create = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(\PDO::FETCH_ASSOC);
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n".($create['Create Table'] ?? '').";\n\n";

            $rows = $pdo->query("SELECT * FROM `{$table}`")->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                $cols = array_map(fn ($c) => "`{$c}`", array_keys($row));
                $vals = array_map(fn ($v) => is_null($v) ? 'NULL' : $pdo->quote((string) $v), array_values($row));
                $sql .= "INSERT INTO `{$table}` (".implode(',', $cols).') VALUES ('.implode(',', $vals).");\n";
            }
            $sql .= "\n";
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        return $sql;
    }
}

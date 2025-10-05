<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Entity;
use League\Csv\Reader;
use App\Models\Transaction;
use App\Models\TransactionType;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $files = [
            database_path('original_data/2024.csv'),
            database_path('original_data/2025.csv'),
        ];

        foreach ($files as $path) {
            if (! file_exists($path)) {
                $this->command->error("CSV file not found: {$path}");
                continue;
            }

            $this->command->info("Importing: {$path}");

            $csv = Reader::createFromPath($path, 'r');
            $csv->setDelimiter(';');
            $csv->setHeaderOffset(0);

            foreach ($csv as $record) {
                // Normalize date (dd.mm.YYYY -> Y-m-d)
                $date = null;
                if (! empty($record['Datum'])) {
                    $date = Carbon::createFromFormat('d.m.Y', trim($record['Datum']))->format('Y-m-d');
                }

                // Normalize entity (Name)
                $entity = null;
                if (! empty($record['Name']) && trim($record['Name']) !== '') {
                    $entityName = trim($record['Name']);
                    $entity = Entity::firstOrCreate(['name' => $entityName]);
                }

                // --- Special handling for Save Back + Steuer ---
                if (! empty($record['Save Back'] ?? null) || ! empty($record['Saveback Steuer'] ?? null)) {
                    $parent = null;

                    if (! empty($record['Save Back'] ?? null)) {
                        $saveBackType = TransactionType::firstOrCreate(['name' => 'Save Back']);
                        $parent = Transaction::create([
                            'date' => $date,
                            'amount' => $this->normalizeAmount($record['Save Back']),
                            'transaction_type_id' => $saveBackType->id,
                            'entity_id' => $entity?->id,
                            'parent_id' => null,
                        ]);
                    }

                    if (! empty($record['Saveback Steuer'] ?? null)) {
                        $steuerType = TransactionType::firstOrCreate(['name' => 'Saveback Steuer']);
                        Transaction::create([
                            'date' => $date,
                            'amount' => $this->normalizeAmount($record['Saveback Steuer']),
                            'transaction_type_id' => $steuerType->id,
                            'entity_id' => $entity?->id,
                            'parent_id' => $parent?->id ?? null,
                        ]);
                    }

                    continue; // handled this row
                }

                // --- Normal single-type transactions (only one column per row expected) ---
                $map = [
                    'Kauf'         => 'Kauf',
                    'Verkauf'      => 'Verkauf',
                    'Dividenden'   => 'Dividenden',
                    'Einzahlungen' => 'Einzahlungen',
                    'Ausgabe'      => 'Ausgabe',
                    'Zinsen'       => 'Zinsen',
                ];

                foreach ($map as $column => $typeName) {
                    if (! empty($record[$column] ?? null) && trim($record[$column]) !== '') {
                        $type = TransactionType::firstOrCreate(['name' => $typeName]);

                        Transaction::create([
                            'date' => $date,
                            'amount' => $this->normalizeAmount($record[$column]),
                            'transaction_type_id' => $type->id,
                            'entity_id' => $entity?->id,
                            'parent_id' => null,
                        ]);

                        break; // only one transaction per row (except SaveBack combo)
                    }
                }
            }
        }

        $this->command->info('✅ All transactions normalized and imported!');
    }

    /**
     * Normalize euro amounts like "€ 50,00" or "1.234,56" to float 50.00 / 1234.56
     *
     * @param string|null $value
     * @return float|null
     */
    private function normalizeAmount($value): ?float
    {
        if (is_null($value)) {
            return null;
        }

        $value = trim($value);
        if ($value === '') {
            return null;
        }

        // keep only digits, dot, comma and minus
        $clean = preg_replace('/[^\d\.\,\-]/u', '', $value);

        if ($clean === '') {
            return null;
        }

        // If both '.' and ',' exist -> assume '.' thousands, ',' decimal (e.g. 1.234,56)
        if (strpos($clean, '.') !== false && strpos($clean, ',') !== false) {
            $clean = str_replace('.', '', $clean); // remove thousands
            $clean = str_replace(',', '.', $clean); // decimal to dot
        } elseif (strpos($clean, ',') !== false && strpos($clean, '.') === false) {
            // only comma present -> treat comma as decimal
            $clean = str_replace(',', '.', $clean);
        }
        // else: only dots or neither -> keep as-is (dot may already be decimal separator)

        return (float) $clean;
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use App\Models\Product;

class FixImagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:images {--check : Only check without fixing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix product images issues (storage link, paths, permissions)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🖼️  Arradea Marketplace - Image Fix Tool');
        $this->info('==========================================');
        $this->newLine();

        $checkOnly = $this->option('check');

        if ($checkOnly) {
            $this->info('🔍 Running in CHECK mode (no changes will be made)');
            $this->newLine();
        }

        // Step 1: Check storage link
        $this->checkStorageLink($checkOnly);

        // Step 2: Check folders
        $this->checkFolders($checkOnly);

        // Step 3: Check permissions
        $this->checkPermissions($checkOnly);

        // Step 4: Check database paths
        $this->checkDatabasePaths($checkOnly);

        // Step 5: Clear cache
        if (!$checkOnly) {
            $this->clearCache();
        }

        // Summary
        $this->newLine();
        $this->info('==========================================');
        
        if ($checkOnly) {
            $this->info('✅ Check completed!');
            $this->newLine();
            $this->comment('Run without --check flag to apply fixes:');
            $this->comment('  php artisan fix:images');
        } else {
            $this->info('✅ Image fix completed!');
            $this->newLine();
            $this->comment('Next steps:');
            $this->comment('1. Test upload gambar baru');
            $this->comment('2. Check apakah gambar lama sudah muncul');
            $this->comment('3. Run: php artisan tinker');
            $this->comment('   >>> \App\Models\Product::latest()->first()->image');
        }

        $this->newLine();

        return Command::SUCCESS;
    }

    /**
     * Check and fix storage link
     */
    protected function checkStorageLink($checkOnly)
    {
        $this->info('📁 Step 1: Checking storage link...');

        $publicStorage = public_path('storage');
        $linkExists = File::exists($publicStorage);

        if ($linkExists) {
            $this->line('   ✅ Storage link exists');
            
            // Check if it's a valid symlink
            if (is_link($publicStorage)) {
                $target = readlink($publicStorage);
                $this->line("   → Target: {$target}");
            }
        } else {
            $this->warn('   ⚠️  Storage link does not exist');

            if (!$checkOnly) {
                $this->line('   Creating storage link...');
                Artisan::call('storage:link');
                $this->info('   ✅ Storage link created');
            } else {
                $this->comment('   → Run: php artisan storage:link');
            }
        }

        $this->newLine();
    }

    /**
     * Check and create folders
     */
    protected function checkFolders($checkOnly)
    {
        $this->info('📂 Step 2: Checking storage folders...');

        $folders = [
            'storage/app/public/products',
            'storage/app/public/categories',
            'storage/app/public/payments',
        ];

        foreach ($folders as $folder) {
            $path = base_path($folder);
            
            if (File::exists($path)) {
                $fileCount = count(File::files($path));
                $this->line("   ✅ {$folder} ({$fileCount} files)");
            } else {
                $this->warn("   ⚠️  {$folder} does not exist");

                if (!$checkOnly) {
                    File::makeDirectory($path, 0775, true);
                    $this->info("   ✅ Created {$folder}");
                } else {
                    $this->comment("   → Will create: {$folder}");
                }
            }
        }

        $this->newLine();
    }

    /**
     * Check permissions
     */
    protected function checkPermissions($checkOnly)
    {
        $this->info('🔐 Step 3: Checking permissions...');

        // Skip on Windows
        if (PHP_OS_FAMILY === 'Windows') {
            $this->line('   ⏭️  Skipped (Windows detected)');
            $this->newLine();
            return;
        }

        $folders = [
            'storage',
            'bootstrap/cache',
        ];

        foreach ($folders as $folder) {
            $path = base_path($folder);
            
            if (File::exists($path)) {
                $perms = substr(sprintf('%o', fileperms($path)), -4);
                $this->line("   {$folder}: {$perms}");

                if (!$checkOnly && $perms !== '0775') {
                    chmod($path, 0775);
                    $this->info("   ✅ Updated permissions to 0775");
                }
            }
        }

        $this->newLine();
    }

    /**
     * Check database image paths
     */
    protected function checkDatabasePaths($checkOnly)
    {
        $this->info('🔍 Step 4: Checking database image paths...');

        try {
            // Check for incorrect paths
            $incorrectPaths = Product::whereNotNull('image')
                ->where('image', 'NOT LIKE', 'http%')
                ->where('image', 'NOT LIKE', '/storage/%')
                ->count();

            if ($incorrectPaths > 0) {
                $this->warn("   ⚠️  Found {$incorrectPaths} products with incorrect paths");

                if (!$checkOnly) {
                    if ($this->confirm('   Fix these paths?', true)) {
                        Product::whereNotNull('image')
                            ->where('image', 'NOT LIKE', 'http%')
                            ->where('image', 'NOT LIKE', '/storage/%')
                            ->update([
                                'image' => \DB::raw("CONCAT('/storage/', image)")
                            ]);
                        
                        $this->info("   ✅ Fixed {$incorrectPaths} image paths");
                    }
                } else {
                    $this->comment('   → Run without --check to fix');
                }
            } else {
                $this->line('   ✅ All image paths look correct');
            }

            // Check for double /storage/
            $doublePaths = Product::where('image', 'LIKE', '/storage/storage/%')->count();

            if ($doublePaths > 0) {
                $this->warn("   ⚠️  Found {$doublePaths} products with double /storage/");

                if (!$checkOnly) {
                    if ($this->confirm('   Fix these paths?', true)) {
                        Product::where('image', 'LIKE', '/storage/storage/%')
                            ->update([
                                'image' => \DB::raw("REPLACE(image, '/storage/storage/', '/storage/')")
                            ]);
                        
                        $this->info("   ✅ Fixed {$doublePaths} double paths");
                    }
                } else {
                    $this->comment('   → Run without --check to fix');
                }
            }

            // Show sample
            $sample = Product::whereNotNull('image')->latest()->first();
            if ($sample) {
                $this->newLine();
                $this->line('   Sample product image:');
                $this->line("   → Raw: {$sample->getAttributes()['image']}");
                $this->line("   → Accessor: {$sample->image}");
            }

        } catch (\Exception $e) {
            $this->error("   ❌ Error checking database: {$e->getMessage()}");
        }

        $this->newLine();
    }

    /**
     * Clear cache
     */
    protected function clearCache()
    {
        $this->info('🧹 Step 5: Clearing cache...');

        Artisan::call('cache:clear');
        $this->line('   ✅ Cache cleared');

        Artisan::call('config:clear');
        $this->line('   ✅ Config cleared');

        Artisan::call('view:clear');
        $this->line('   ✅ View cleared');

        $this->newLine();
    }
}

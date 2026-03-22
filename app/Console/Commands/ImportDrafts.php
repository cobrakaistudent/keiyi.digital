<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportDrafts extends Command
{
    protected $signature = 'drafts:import {path? : Path to drafts directory}';
    protected $description = 'Import William markdown drafts into the posts table for editorial review';

    public function handle(): int
    {
        $path = $this->argument('path')
            ?? base_path('agent/william_drafts');

        if (!is_dir($path)) {
            $this->error("Directory not found: {$path}");
            return 1;
        }

        $files = glob($path . '/*.md');
        $imported = 0;
        $skipped = 0;

        foreach ($files as $file) {
            $filename = basename($file);

            // Skip if already imported
            if (Post::where('source_file', $filename)->exists()) {
                $this->line("  ⏭ {$filename} (ya importado)");
                $skipped++;
                continue;
            }

            // Skip JSON-format drafts (old William, pre-upgrade)
            $raw = file_get_contents($file);
            if (str_starts_with(trim($raw), '```json') || str_starts_with(trim($raw), '{')) {
                $this->line("  ⏭ {$filename} (formato JSON viejo, omitido)");
                $skipped++;
                continue;
            }

            $md = file_get_contents($file);

            // Strip HTML comment header
            $md = preg_replace('/^<!--.*?-->\n*/s', '', $md);

            // Extract title from first # heading
            $title = 'Sin título';
            if (preg_match('/^#\s+(.+)$/m', $md, $m)) {
                $title = trim($m[1]);
            }

            // Extract first paragraph as excerpt
            $lines = array_filter(explode("\n", $md), function ($l) {
                $l = trim($l);
                return $l && !str_starts_with($l, '#') && !str_starts_with($l, '---')
                    && !str_starts_with($l, 'category:') && !str_starts_with($l, 'word_count:')
                    && !str_starts_with($l, 'Categoria:');
            });
            $excerpt = Str::limit(reset($lines) ?: '', 250);

            // Extract category
            $category = 'Marketing Digital';
            if (preg_match('/category:\s*(.+)/i', $md, $m)) {
                $category = trim(explode('|', $m[1])[0]);
            }

            // Clean content (remove metadata footer)
            $content = preg_replace('/^---\s*\n?category:.*$/m', '', $md);
            $content = preg_replace('/^word_count:.*$/m', '', $content);
            $content = preg_replace('/^Categoria:.*$/m', '', $content);
            $content = trim($content);

            // Convert markdown to HTML for the blog
            $html = Str::markdown($content);

            // Ensure unique slug
            $slug = Str::slug($title);
            $counter = 1;
            while (Post::where('slug', $slug)->exists()) {
                $slug = Str::slug($title) . '-' . $counter++;
            }

            Post::create([
                'title'       => $title,
                'slug'        => $slug,
                'excerpt'     => $excerpt,
                'content'     => $html,
                'category'    => $category,
                'status'      => 'draft',
                'source_file' => $filename,
                'word_count'  => str_word_count(strip_tags($html)),
            ]);

            $this->info("  ✅ {$filename} → \"{$title}\"");
            $imported++;
        }

        $this->newLine();
        $this->info("Importados: {$imported} | Omitidos: {$skipped} | Total archivos: " . count($files));

        return 0;
    }
}

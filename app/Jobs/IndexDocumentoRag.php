<?php

namespace App\Jobs;

use App\Models\Documento;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class IndexDocumentoRag implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $documentoId) {}

    public function handle(): void
    {
        Log::info("IndexDocumentoRag START", ['documentoId' => $this->documentoId]);

        $doc = Documento::find($this->documentoId);

        if (!$doc) {
            Log::warning("IndexDocumentoRag: documento no existe", ['id' => $this->documentoId]);
            return;
        }

        if (!$doc->archivo_path || !Storage::disk('public')->exists($doc->archivo_path)) {
            Log::warning("IndexDocumentoRag: archivo no existe", ['id' => $doc->id, 'path' => $doc->archivo_path]);
            return;
        }

        $absPath = Storage::disk('public')->path($doc->archivo_path);

        // 1) Extraer texto del PDF
        $text = $this->extractPdfText($absPath);
        $lenText = is_string($text) ? strlen($text) : 0;

        Log::info("IndexDocumentoRag TEXT LEN", [
            'doc' => $doc->id,
            'len' => $lenText,
            'absPath' => $absPath,
        ]);

        if (!is_string($text) || trim($text) === '') {
            Log::warning("IndexDocumentoRag: texto vacío", ['id' => $doc->id]);
            return;
        }

        // 2) Normalizar texto
        $text = preg_replace("/[ \t]+/", " ", $text);
        $text = preg_replace("/\r\n|\r/", "\n", $text);

        $chunks = $this->chunkText($text, 900, 150);
        Log::info("IndexDocumentoRag CHUNKS", ['doc' => $doc->id, 'chunks' => count($chunks)]);

        if (count($chunks) === 0) {
            Log::warning("IndexDocumentoRag: no generó chunks", ['doc' => $doc->id]);
            return;
        }

        // 3) Reindex: borrar chunks previos del doc
        $deleted = DB::table('document_chunks')->where('documento_id', $doc->id)->delete();
        Log::info("IndexDocumentoRag DELETE OLD", ['doc' => $doc->id, 'deleted' => $deleted]);

        // 4) Config Ollama
        $ollama = rtrim(env('OLLAMA_URL', 'http://127.0.0.1:11434'), '/');

        // ✅ Lee la variable correcta de tu .env
        $model = env('OLLAMA_EMBED_MODEL', 'nomic-embed-text');

        Log::info("IndexDocumentoRag OLLAMA CONF", [
            'doc' => $doc->id,
            'ollama' => $ollama,
            'model' => $model,
        ]);

        $inserted = 0;
        $embeddedOk = 0;
        $embeddedFail = 0;

        foreach ($chunks as $i => $chunk) {
            // Log del chunk (solo tamaño para no llenar el log)
            Log::info("IndexDocumentoRag LOOP", ['doc' => $doc->id, 'i' => $i, 'chars' => strlen($chunk)]);

            $emb = $this->embed($ollama, $model, $chunk);

            if (is_array($emb) && count($emb) > 0) {
                $embeddedOk++;
            } else {
                $embeddedFail++;
            }

            DB::table('document_chunks')->insert([
                'documento_id' => $doc->id,
                'chunk_index' => $i,
                'texto' => $chunk,
                'embedding' => $emb ? json_encode($emb) : null,
                'embedding_model' => $model,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $inserted++;
        }

        Log::info("IndexDocumentoRag OK", [
            'doc' => $doc->id,
            'chunks_total' => count($chunks),
            'inserted' => $inserted,
            'embedded_ok' => $embeddedOk,
            'embedded_fail' => $embeddedFail,
        ]);
    }

    private function extractPdfText(string $absPath): string
    {
        $cmd = 'pdftotext -layout ' . escapeshellarg($absPath) . ' -';
        $out = @shell_exec($cmd);

        return is_string($out) ? $out : '';
    }

    private function chunkText(string $text, int $size, int $overlap): array
    {
        $text = trim($text);
        if ($text === '') return [];

        $chunks = [];
        $len = mb_strlen($text, 'UTF-8');
        $start = 0;

        while ($start < $len) {
            $end = min($start + $size, $len);
            $chunk = mb_substr($text, $start, $end - $start, 'UTF-8');
            $chunk = trim($chunk);

            if ($chunk !== '') $chunks[] = $chunk;

            if ($end >= $len) break;
            $start = max(0, $end - $overlap);
        }

        return $chunks;
    }

    private function embed(string $ollamaBase, string $model, string $prompt): ?array
    {
        try {
            $res = Http::timeout(120)->post($ollamaBase . '/api/embeddings', [
                'model' => $model,
                'prompt' => $prompt,
            ]);

            if (!$res->ok()) {
                Log::warning("IndexDocumentoRag embed NOT OK", [
                    'status' => $res->status(),
                    'body' => $res->body(),
                ]);
                return null;
            }

            $json = $res->json();
            $emb = $json['embedding'] ?? null;

            if (!is_array($emb)) {
                Log::warning("IndexDocumentoRag embed missing embedding key", [
                    'json_keys' => is_array($json) ? array_keys($json) : 'not_array',
                ]);
                return null;
            }

            return $emb;
        } catch (\Throwable $e) {
            Log::error("IndexDocumentoRag embed error", [
                'msg' => $e->getMessage(),
                'class' => get_class($e),
            ]);
            return null;
        }
    }
}

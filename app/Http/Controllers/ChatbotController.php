<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    // ─── Configuración ────────────────────────────────────────────────────────
    private string $ollamaUrl;
    private string $embedModel;
    private string $chatModel;
    private int    $topK          = 5;     // cuántos chunks recuperar
    private float  $minSimilarity = 0.30;  // umbral mínimo de relevancia

    public function __construct()
    {
        $this->ollamaUrl  = rtrim(env('OLLAMA_URL', 'http://127.0.0.1:11434'), '/');
        $this->embedModel = env('OLLAMA_EMBED_MODEL', 'nomic-embed-text');
        $this->chatModel  = env('CHATBOT_MODEL', 'phi3:mini');
    }

    // ─── Endpoint principal ───────────────────────────────────────────────────
    public function preguntar(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:1000',
        ]);

        $pregunta = trim($request->input('question'));

        try {
            // 1. Generar embedding de la pregunta
            $embeddingPregunta = $this->generarEmbedding($pregunta);

            if (empty($embeddingPregunta)) {
                return response()->json([
                    'answer' => '⚠️ No pude procesar tu pregunta. Verifica que Ollama esté corriendo.'
                ], 503);
            }

            // 2. Buscar chunks relevantes por similitud coseno
            $chunks = $this->buscarChunksRelevantes($embeddingPregunta);

            // 3. Armar el contexto con los chunks encontrados
            $contexto = $this->armarContexto($chunks);

            // 4. Llamar al LLM con la pregunta + contexto
            $respuesta = $this->llamarLLM($pregunta, $contexto, empty($chunks));

            return response()->json([
                'answer'        => $respuesta,
                'chunks_usados' => count($chunks),
            ]);

        } catch (\Exception $e) {
            Log::error('ChatbotController error: ' . $e->getMessage());

            return response()->json([
                'answer' => '❌ Error interno del asistente. Por favor intenta de nuevo.'
            ], 500);
        }
    }

    // ─── Generar embedding via Ollama ─────────────────────────────────────────
    private function generarEmbedding(string $texto): array
    {
        $response = Http::timeout(30)->post("{$this->ollamaUrl}/api/embeddings", [
            'model'  => $this->embedModel,
            'prompt' => $texto,
        ]);

        if (!$response->successful()) {
            Log::warning('Ollama embeddings falló: ' . $response->body());
            return [];
        }

        return $response->json('embedding', []);
    }

    // ─── Buscar chunks relevantes en BD ───────────────────────────────────────
    private function buscarChunksRelevantes(array $embeddingPregunta): array
    {
        // Traemos todos los chunks activos (con documentos activos)
        $chunks = DB::table('document_chunks')
            ->join('documentos', 'document_chunks.documento_id', '=', 'documentos.id')
            ->whereNull('documentos.deleted_at')
            ->where('documentos.estado', 'activo')
            ->select(
                'document_chunks.id',
                'document_chunks.texto',
                'document_chunks.embedding',
                'documentos.titulo as doc_titulo',
                'documentos.id as doc_id'
            )
            ->get();

        if ($chunks->isEmpty()) {
            return [];
        }

        // Calcular similitud coseno para cada chunk
        $resultados = [];

        foreach ($chunks as $chunk) {
            $embeddingChunk = json_decode($chunk->embedding, true);

            if (empty($embeddingChunk)) continue;

            $similitud = $this->cosineSimilarity($embeddingPregunta, $embeddingChunk);

            if ($similitud >= $this->minSimilarity) {
                $resultados[] = [
                    'texto'      => $chunk->texto,
                    'doc_titulo' => $chunk->doc_titulo,
                    'doc_id'     => $chunk->doc_id,
                    'similitud'  => $similitud,
                ];
            }
        }

        // Ordenar por similitud descendente y tomar los top K
        usort($resultados, fn($a, $b) => $b['similitud'] <=> $a['similitud']);

        return array_slice($resultados, 0, $this->topK);
    }

    // ─── Similitud coseno ─────────────────────────────────────────────────────
    private function cosineSimilarity(array $a, array $b): float
    {
        if (count($a) !== count($b) || empty($a)) return 0.0;

        $dot    = 0.0;
        $normA  = 0.0;
        $normB  = 0.0;

        foreach ($a as $i => $val) {
            $dot   += $val * $b[$i];
            $normA += $val * $val;
            $normB += $b[$i] * $b[$i];
        }

        $denom = sqrt($normA) * sqrt($normB);

        return $denom > 0 ? round($dot / $denom, 6) : 0.0;
    }

    // ─── Armar contexto para el LLM ───────────────────────────────────────────
    private function armarContexto(array $chunks): string
    {
        if (empty($chunks)) return '';

        $partes = [];
        $docsVistos = [];

        foreach ($chunks as $i => $chunk) {
            $num = $i + 1;
            $titulo = $chunk['doc_titulo'];

            // Mostrar título del documento solo la primera vez
            $encabezado = !in_array($chunk['doc_id'], $docsVistos)
                ? "📄 Documento: {$titulo}\n"
                : '';

            $docsVistos[] = $chunk['doc_id'];

            $partes[] = "{$encabezado}[Fragmento {$num}]\n{$chunk['texto']}";
        }

        return implode("\n\n---\n\n", $partes);
    }

    // ─── Llamar al LLM (phi3:mini) ────────────────────────────────────────────
    private function llamarLLM(string $pregunta, string $contexto, bool $sinContexto): string
    {
        if ($sinContexto) {
            $systemPrompt = "Eres el asistente virtual de la Cámara de Comercio de Venezuela (CCV). 
Responde de forma amable y profesional en español.
Si no tienes información suficiente para responder, indícalo claramente y sugiere buscar en el portal.";

            $userMessage = $pregunta;
        } else {
            $systemPrompt = "Eres el asistente virtual de la Cámara de Comercio de Venezuela (CCV).
Tu función es responder preguntas sobre los documentos institucionales del portal.

INSTRUCCIONES:
- Responde SIEMPRE en español, de forma clara y profesional.
- Basa tu respuesta ÚNICAMENTE en la información del contexto proporcionado.
- Si el contexto no contiene la información necesaria, dilo claramente.
- Cita el nombre del documento cuando sea relevante.
- Sé conciso pero completo. Usa listas cuando ayude a la claridad.
- No inventes información que no esté en el contexto.";

            $userMessage = "CONTEXTO DE DOCUMENTOS:\n{$contexto}\n\n---\n\nPREGUNTA: {$pregunta}";
        }

        $response = Http::timeout(120)->post("{$this->ollamaUrl}/api/chat", [
            'model'  => $this->chatModel,
            'stream' => false,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user',   'content' => $userMessage],
            ],
            'options' => [
                'temperature' => 0.3,  // respuestas más precisas y consistentes
                'num_predict' => 512,  // longitud máxima de respuesta
            ],
        ]);

        if (!$response->successful()) {
            Log::error('Ollama chat falló: ' . $response->body());
            return '⚠️ El modelo de IA no está disponible en este momento.';
        }

        return $response->json('message.content', '⚠️ No se obtuvo respuesta del modelo.');
    }
}

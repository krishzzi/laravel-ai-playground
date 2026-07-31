/**
 * Browser-only file ingestion pipeline.
 *
 * Goal: a user attaches a PDF/DOCX/CSV/image/audio file and we extract
 * whatever the model actually needs (plain text, a small base64 image, a
 * WebGPU/WASM-computed embedding) entirely client-side. The raw file is
 * held only in memory (or IndexedDB for the session) and is NEVER POSTed to
 * the server unless the specific provider call truly requires binary bytes
 * it cannot get any other way (e.g. sending an image to a vision model, or
 * TTS/STT audio). Even then we upload the minimum necessary payload, once,
 * directly to the provider-facing endpoint — not to general storage.
 *
 * Libraries expected to be bundled (see resources/js/vendor):
 *  - pdf.js            → PDF text extraction
 *  - mammoth.js         → DOCX -> HTML/text
 *  - papaparse           → CSV parsing
 *  - @xenova/transformers (WebGPU/WASM) → optional local embeddings for
 *    quick client-side semantic chunk ranking before we even send text up
 */

export async function ingestFileInBrowser(file) {
    const base = { name: file.name, mime: file.type, size: file.size };

    if (file.type === 'application/pdf') {
        return { ...base, kind: 'document', extracted_text: await extractPdfText(file) };
    }

    if (file.type.includes('word') || file.name.endsWith('.docx')) {
        return { ...base, kind: 'document', extracted_text: await extractDocxText(file) };
    }

    if (file.type === 'text/csv' || file.name.endsWith('.csv')) {
        return { ...base, kind: 'table', extracted_text: await extractCsvPreview(file) };
    }

    if (file.type.startsWith('text/') || file.name.match(/\.(md|json|ya?ml|txt|log)$/)) {
        return { ...base, kind: 'document', extracted_text: await file.text() };
    }

    if (file.type.startsWith('image/')) {
        // Vision models need actual pixels — this is the one case we keep
        // as base64 and pass through, but still never touch server storage;
        // it goes straight into the provider attachment payload.
        return { ...base, kind: 'image', vision_base64: await toBase64(file), preview_url: URL.createObjectURL(file) };
    }

    if (file.type.startsWith('audio/')) {
        return { ...base, kind: 'audio', requires_upload_for: 'stt', local_ref: URL.createObjectURL(file) };
    }

    return { ...base, kind: 'unknown', extracted_text: null };
}

async function extractPdfText(file) {
    const pdfjsLib = window.pdfjsLib;
    const buf = await file.arrayBuffer();
    const doc = await pdfjsLib.getDocument({ data: buf }).promise;
    let text = '';
    for (let i = 1; i <= Math.min(doc.numPages, 200); i++) {
        const page = await doc.getPage(i);
        const content = await page.getTextContent();
        text += content.items.map((it) => it.str).join(' ') + '\n';
    }
    return text.slice(0, 200_000); // guardrail against megatoken dumps
}

async function extractDocxText(file) {
    const buf = await file.arrayBuffer();
    const result = await window.mammoth.extractRawText({ arrayBuffer: buf });
    return result.value.slice(0, 200_000);
}

async function extractCsvPreview(file) {
    const text = await file.text();
    const parsed = window.Papa.parse(text, { header: true, preview: 200 });
    return JSON.stringify(parsed.data);
}

function toBase64(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = () => resolve(reader.result.split(',')[1]);
        reader.onerror = reject;
        reader.readAsDataURL(file);
    });
}

window.ingestFileInBrowser = ingestFileInBrowser;

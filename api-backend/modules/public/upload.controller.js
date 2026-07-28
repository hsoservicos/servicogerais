// ═══════════════════════════════════════════════════════════════
// modules/public/upload.controller.js — Upload de Fotos (Story 6.2)
// ═══════════════════════════════════════════════════════════════
// Upload de imagens para leads via wizard de solicitação.
// Endpoint público com rate limiting + validação de tipo/tamanho.
// ═══════════════════════════════════════════════════════════════

const path = require('path');
const fs = require('fs');
const multer = require('multer');
const { v4: uuidv4 } = require('uuid');

// ── Configuração do Diretório de Upload ────────────────────
const UPLOADS_DIR = path.join(__dirname, '..', '..', 'uploads');
const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
const ALLOWED_EXTENSIONS = ['.jpg', '.jpeg', '.png', '.webp', '.gif'];

// Garantir que o diretório de uploads existe
if (!fs.existsSync(UPLOADS_DIR)) {
  fs.mkdirSync(UPLOADS_DIR, { recursive: true });
  console.log(`[Upload] 📁 Diretório criado: ${UPLOADS_DIR}`);
}

// ── Storage Engine ──────────────────────────────────────────
const storage = multer.diskStorage({
  destination: (req, file, cb) => {
    cb(null, UPLOADS_DIR);
  },
  filename: (req, file, cb) => {
    const ext = path.extname(file.originalname).toLowerCase();
    // Nome único: UUID + extensão original
    const uniqueName = `${uuidv4()}${ext}`;
    cb(null, uniqueName);
  },
});

// ── File Filter ─────────────────────────────────────────────
function fileFilter(req, file, cb) {
  const ext = path.extname(file.originalname).toLowerCase();

  if (!ALLOWED_TYPES.includes(file.mimetype) && !ALLOWED_EXTENSIONS.includes(ext)) {
    return cb(new Error(`Tipo de arquivo não permitido: ${file.mimetype}. Use JPEG, PNG, WebP ou GIF.`), false);
  }

  cb(null, true);
}

// ── Multer Middleware Configurado ───────────────────────────
const upload = multer({
  storage,
  limits: {
    fileSize: MAX_FILE_SIZE,
    files: 5, // Máximo 5 arquivos por requisição
  },
  fileFilter,
}).array('photos', 5);

// ── Handler com tratamento de erro do multer ────────────────
function uploadHandler(req, res, next) {
  upload(req, res, (err) => {
    if (err instanceof multer.MulterError) {
      // Erro específico do multer
      if (err.code === 'LIMIT_FILE_SIZE') {
        return res.status(413).json({
          error: 'ERR_FILE_TOO_LARGE',
          message: 'Arquivo muito grande. O tamanho máximo é 5MB por arquivo.',
        });
      }
      if (err.code === 'LIMIT_FILE_COUNT') {
        return res.status(413).json({
          error: 'ERR_TOO_MANY_FILES',
          message: 'Máximo de 5 arquivos por solicitação.',
        });
      }
      if (err.code === 'LIMIT_UNEXPECTED_FILE') {
        return res.status(400).json({
          error: 'ERR_UNEXPECTED_FIELD',
          message: 'Campo de arquivo inesperado. Use o campo "photos".',
        });
      }
      return res.status(400).json({
        error: 'ERR_UPLOAD',
        message: err.message,
      });
    }

    if (err) {
      // Erro de validação (fileFilter)
      return res.status(415).json({
        error: 'ERR_UNSUPPORTED_TYPE',
        message: err.message || 'Tipo de arquivo não suportado.',
      });
    }

    // Sem arquivos enviados
    if (!req.files || req.files.length === 0) {
      return res.status(400).json({
        error: 'ERR_NO_FILES',
        message: 'Nenhum arquivo enviado.',
      });
    }

    // Sucesso — retornar URLs dos arquivos
    const files = req.files.map((file) => ({
      originalName: file.originalname,
      filename: file.filename,
      size: file.size,
      mimetype: file.mimetype,
      url: `/api/v1/public/uploads/${file.filename}`,
    }));

    res.status(201).json({
      message: `${files.length} arquivo(s) enviado(s) com sucesso`,
      data: {
        files,
      },
      correlationId: req.correlationId,
    });
  });
}

module.exports = { uploadHandler };

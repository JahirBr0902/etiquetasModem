-- Script de base de datos - Witmac Etiquetas (Versión Inventario Pro)

-- 1. Templates de Etiquetas
CREATE TABLE IF NOT EXISTS etiquetas_templates (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    ancho FLOAT DEFAULT 50, -- mm
    alto FLOAT DEFAULT 30,  -- mm
    config_json TEXT,       -- Fuentes, tamaños, posiciones, etc.
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Modelos de Modem y su Configuración
CREATE TABLE IF NOT EXISTS modelos_modem (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    cant_etiquetas INTEGER DEFAULT 1,
    etiqueta_primaria_id INTEGER REFERENCES etiquetas_templates(id),
    etiqueta_secundaria_id INTEGER REFERENCES etiquetas_templates(id),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Lotes
CREATE TABLE IF NOT EXISTS lotes (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    estado VARCHAR(20) DEFAULT 'NUEVO',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_finalizacion TIMESTAMP
);

-- 4. Modems (Maestro de Inventario)
CREATE TABLE IF NOT EXISTS modems (
    id SERIAL PRIMARY KEY,
    modelo_id INTEGER REFERENCES modelos_modem(id),
    sn VARCHAR(50) UNIQUE NOT NULL,
    ssid VARCHAR(50) NOT NULL,
    password VARCHAR(50) NOT NULL,
    estado_inventario VARCHAR(20) DEFAULT 'BODEGA', -- BODEGA, PRESTADO, DAÑADO
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 5. Tabla Intermedia Lote-Modem (Para impresión)
CREATE TABLE IF NOT EXISTS lote_modems (
    id SERIAL PRIMARY KEY,
    lote_id INTEGER REFERENCES lotes(id) ON DELETE CASCADE,
    modem_id INTEGER REFERENCES modems(id) ON DELETE CASCADE,
    estado_impresion VARCHAR(20) DEFAULT 'PENDIENTE', -- PENDIENTE, IMPRESO
    fecha_asignacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(lote_id, modem_id)
);

-- 6. Historial de Movimientos
CREATE TABLE IF NOT EXISTS historial_modems (
    id SERIAL PRIMARY KEY,
    modem_id INTEGER REFERENCES modems(id) ON DELETE CASCADE,
    tipo_movimiento VARCHAR(50) NOT NULL, -- REGISTRO, ASIGNACION_LOTE, PRESTAMO, DEVOLUCION, DAÑO, REPARACION
    lote_id INTEGER REFERENCES lotes(id) ON DELETE SET NULL,
    notas TEXT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertar algunos templates de prueba
INSERT INTO etiquetas_templates (nombre, ancho, alto, config_json) VALUES 
('Estándar Witmac', 50, 25, '{"font":"Inter","fontSize":10,"color":"#000"}'),
('Pequeña Secundaria', 30, 15, '{"font":"Arial","fontSize":8,"color":"#000"}');

-- Insertar algunos modelos de prueba
INSERT INTO modelos_modem (nombre, cant_etiquetas, etiqueta_primaria_id) VALUES 
('Huawei B311', 1, 1),
('ZTE MF279', 2, 1);

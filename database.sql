-- Script de base de datos - Witmac Etiquetas (Versión Pro)

-- 1. Templates de Etiquetas
CREATE TABLE IF NOT EXISTS etiquetas_templates (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    ancho FLOAT DEFAULT 50, -- mm
    alto FLOAT DEFAULT 30,  -- mm
    config_json TEXT,       -- Aquí guardaremos fuentes, tamaños, posiciones, etc.
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
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4. Modems (Actualizado para usar modelo_id)
CREATE TABLE IF NOT EXISTS modems (
    id SERIAL PRIMARY KEY,
    lote_id INTEGER REFERENCES lotes(id) ON DELETE CASCADE,
    modelo_id INTEGER REFERENCES modelos_modem(id),
    sn VARCHAR(50) UNIQUE NOT NULL,
    ssid VARCHAR(50) NOT NULL,
    password VARCHAR(50) NOT NULL,
    estado VARCHAR(20) DEFAULT 'PENDIENTE',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertar algunos templates de prueba
INSERT INTO etiquetas_templates (nombre, ancho, alto, config_json) VALUES 
('Estándar Witmac', 50, 25, '{"font":"Inter","fontSize":10,"color":"#000"}'),
('Pequeña Secundaria', 30, 15, '{"font":"Arial","fontSize":8,"color":"#000"}');

-- Insertar algunos modelos de prueba
INSERT INTO modelos_modem (nombre, cant_etiquetas, etiqueta_primaria_id) VALUES 
('Huawei B311', 1, 1),
('ZTE MF279', 2, 1);

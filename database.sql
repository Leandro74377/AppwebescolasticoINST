-- Script para crear la base de datos y las tablas necesarias
CREATE DATABASE IF NOT EXISTS sistema_escolastico CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE sistema_escolastico;

-- Tabla alumnos
CREATE TABLE IF NOT EXISTS alumnos (
  id_alumno INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  apellido VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla asistencias
CREATE TABLE IF NOT EXISTS asistencias (
  id_asistencia INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_alumno INT UNSIGNED NOT NULL,
  materia VARCHAR(150) NOT NULL,
  creditos INT UNSIGNED DEFAULT 0,
  horas_asistidas INT UNSIGNED DEFAULT 0,
  horas_justificadas INT UNSIGNED DEFAULT 0,
  horas_injustificadas INT UNSIGNED DEFAULT 0,
  porcentaje_asistencia DECIMAL(5,2) DEFAULT 0.00,
  CONSTRAINT fk_asistencias_alumnos FOREIGN KEY (id_alumno)
    REFERENCES alumnos(id_alumno)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

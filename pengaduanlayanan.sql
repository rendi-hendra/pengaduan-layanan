-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               PostgreSQL 17.7 on x86_64-windows, compiled by msvc-19.44.35221, 64-bit
-- Server OS:                    
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES  */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table public.asuransi
DROP TABLE IF EXISTS "asuransi";
CREATE TABLE IF NOT EXISTS "asuransi" (
	"id" INTEGER NOT NULL,
	"nama_asuransi" VARCHAR(50) NULL DEFAULT NULL,
	PRIMARY KEY ("id")
);

-- Dumping data for table public.asuransi: -1 rows
DELETE FROM "asuransi";
/*!40000 ALTER TABLE "asuransi" DISABLE KEYS */;
INSERT INTO "asuransi" ("id", "nama_asuransi") VALUES
	(1, 'BPJS'),
	(2, 'UMUM');
/*!40000 ALTER TABLE "asuransi" ENABLE KEYS */;

-- Dumping structure for table public.formulir
DROP TABLE IF EXISTS "formulir";
CREATE TABLE IF NOT EXISTS "formulir" (
	"id" BIGINT NOT NULL,
	"alamat" TEXT NULL DEFAULT NULL,
	"no_hp" VARCHAR(30) NULL DEFAULT NULL,
	"masukan" TEXT NULL DEFAULT NULL,
	"created_at" TIMESTAMP NULL DEFAULT now(),
	"tanggal" DATE NULL DEFAULT NULL,
	"profil_id" BIGINT NULL DEFAULT NULL,
	PRIMARY KEY ("id"),
	CONSTRAINT "fk_formulir_profil" FOREIGN KEY ("profil_id") REFERENCES "profil" ("id") ON UPDATE NO ACTION ON DELETE NO ACTION
);

-- Dumping data for table public.formulir: -1 rows
DELETE FROM "formulir";
/*!40000 ALTER TABLE "formulir" DISABLE KEYS */;
/*!40000 ALTER TABLE "formulir" ENABLE KEYS */;

-- Dumping structure for table public.keluhan
DROP TABLE IF EXISTS "keluhan";
CREATE TABLE IF NOT EXISTS "keluhan" (
	"id" SERIAL NOT NULL,
	"alamat" VARCHAR NOT NULL,
	"no_hp" VARCHAR NOT NULL,
	"masukan" TEXT NOT NULL,
	"pukul" TIME NULL DEFAULT NULL,
	"tanggal" DATE NULL DEFAULT NULL,
	PRIMARY KEY ("id")
);

-- Dumping data for table public.keluhan: 1 rows
DELETE FROM "keluhan";
/*!40000 ALTER TABLE "keluhan" DISABLE KEYS */;
INSERT INTO "keluhan" ("id", "alamat", "no_hp", "masukan", "pukul", "tanggal") VALUES
	(1, 'Jl. Pahlawan 42', '071312412312', 'asdadasdasdasdas asdasdadwdqwfwqvqw', '08:55:00', '2026-01-29'),
	(2, 'Jl. Pahlawan 42', '071312412312', 'asdqwidqiwd qwdqwidj qwjdnjjasd', '09:03:00', '2026-01-29');
/*!40000 ALTER TABLE "keluhan" ENABLE KEYS */;

-- Dumping structure for table public.kepuasan
DROP TABLE IF EXISTS "kepuasan";
CREATE TABLE IF NOT EXISTS "kepuasan" (
	"id" SERIAL NOT NULL,
	"created_at" TIMESTAMP NOT NULL DEFAULT now(),
	"survey_date" DATE NOT NULL,
	"survey_time" VARCHAR(20) NOT NULL,
	"gender" VARCHAR(10) NOT NULL,
	"education" VARCHAR(10) NOT NULL,
	"jobs" TEXT NOT NULL,
	"services" TEXT NOT NULL,
	"q1" SMALLINT NOT NULL,
	"q2" SMALLINT NOT NULL,
	"q3" SMALLINT NOT NULL,
	"q4" SMALLINT NULL DEFAULT NULL,
	"q5" SMALLINT NOT NULL,
	"q6" SMALLINT NOT NULL,
	"q7" SMALLINT NOT NULL,
	"q8" SMALLINT NOT NULL,
	"q9" SMALLINT NOT NULL,
	"penjamin" VARCHAR NULL DEFAULT 'UMUM',
	PRIMARY KEY ("id"),
	CONSTRAINT "chk_q1" CHECK (((q1 >= 1) AND (q1 <= 4))),
	CONSTRAINT "chk_q2" CHECK (((q2 >= 1) AND (q2 <= 4))),
	CONSTRAINT "chk_q3" CHECK (((q3 >= 1) AND (q3 <= 4))),
	CONSTRAINT "chk_q5" CHECK (((q5 >= 1) AND (q5 <= 4))),
	CONSTRAINT "chk_q6" CHECK (((q6 >= 1) AND (q6 <= 4))),
	CONSTRAINT "chk_q7" CHECK (((q7 >= 1) AND (q7 <= 4))),
	CONSTRAINT "chk_q8" CHECK (((q8 >= 1) AND (q8 <= 4))),
	CONSTRAINT "chk_q9" CHECK (((q9 >= 1) AND (q9 <= 4))),
	CONSTRAINT "chk_q4" CHECK (((q4 >= 1) AND (q4 <= 4)))
);

-- Dumping data for table public.kepuasan: 0 rows
DELETE FROM "kepuasan";
/*!40000 ALTER TABLE "kepuasan" DISABLE KEYS */;
INSERT INTO "kepuasan" ("id", "created_at", "survey_date", "survey_time", "gender", "education", "jobs", "services", "q1", "q2", "q3", "q4", "q5", "q6", "q7", "q8", "q9", "penjamin") VALUES
	(16, '2026-01-28 13:43:44.432164', '2026-01-28', '12-18', 'male', 's1', 'pelajar', 'radiologi', 1, 4, 4, 3, 2, 4, 1, 1, 4, 'bpjs'),
	(17, '2026-01-28 10:18:45.06348', '2026-01-20', '08-12', 'female', 'S1', 'Ibu Rumah Tangga', 'Poli Umum', 3, 3, 3, 2, 3, 3, 3, 2, 3, NULL),
	(18, '2026-01-28 10:18:45.06348', '2026-01-21', '08-12', 'female', 'SMA', 'Mahasiswa', 'Apotek', 3, 4, 3, 3, 4, 3, 3, 4, 3, NULL),
	(19, '2026-01-28 10:18:45.06348', '2026-01-22', '12-18', 'female', 'D3', 'Perawat', 'Pendaftaran', 2, 3, 3, 2, 3, 3, 2, 3, 3, NULL),
	(20, '2026-01-28 10:18:45.06348', '2026-01-21', '12-18', 'male', 'SMP', 'Petani', 'Poli Gigi', 2, 3, 2, 3, 2, 3, 2, 3, 2, NULL),
	(21, '2026-01-28 10:18:45.06348', '2026-01-22', '08-12', 'male', 'SMA', 'Supir', 'IGD', 3, 3, 4, 4, 3, 4, 3, 4, 3, NULL),
	(22, '2026-01-28 10:18:45.06348', '2026-01-23', '12-18', 'female', 'SMA', 'Pedagang', 'Apotek', 3, 3, 3, 3, 3, 2, 3, 1, 3, NULL),
	(23, '2026-01-28 10:18:45.06348', '2026-01-23', '08-12', 'male', 'S1', 'PNS', 'Poli Penyakit Dalam', 4, 4, 4, 3, 4, 1, 4, 3, 4, NULL),
	(24, '2026-01-28 10:18:45.06348', '2026-01-20', '08-12', 'male', 'SMA', 'Karyawan Swasta', 'Pendaftaran', 4, 4, 3, 1, 3, 2, 4, 3, 4, NULL),
	(25, '2026-01-28 10:09:44.123517', '2026-01-28', '08-12', 'male', 's1', 'wirausaha,pelajar', 'admissi', 4, 1, 2, 4, 2, 3, 2, 1, 4, NULL),
	(26, '2026-01-28 10:18:45.06348', '2026-01-21', '12-18', 'male', 'D3', 'Wiraswasta', 'Laboratorium', 4, 4, 4, 3, 1, 4, 2, 4, 4, NULL),
	(27, '2026-01-28 10:18:45.06348', '2026-01-22', '08-12', 'female', 'S1', 'Guru', 'Poli Anak', 4, 1, 4, 2, 4, 4, 3, 4, 4, NULL),
	(28, '2026-01-28 13:19:30.296116', '2026-01-28', '12-18', 'male', 'sma', 'wirausaha', 'igd', 4, 2, 4, 3, 3, 2, 4, 3, 4, NULL),
	(29, '2026-01-28 08:23:05.114995', '2026-01-28', '08-12', 'male', 's1', 'pelajar', 'admissi', 3, 4, 3, 3, 3, 4, 3, 3, 4, ''),
	(31, '2026-01-29 07:59:11.877266', '2026-01-29', '12-18', 'male', 's1', 'swasta,pelajar', 'rawat_jalan', 4, 3, 4, NULL, 2, 4, 1, 4, 4, 'BPJS'),
	(32, '2026-01-29 08:04:09.189078', '2026-01-29', '08-12', 'female', 'sma', 'swasta', 'igd', 4, 3, 4, NULL, 1, 4, 2, 3, 3, 'BPJS');
/*!40000 ALTER TABLE "kepuasan" ENABLE KEYS */;

-- Dumping structure for table public.layanan
DROP TABLE IF EXISTS "layanan";
CREATE TABLE IF NOT EXISTS "layanan" (
	"id" INTEGER NOT NULL,
	"nama_layanan" VARCHAR(100) NULL DEFAULT NULL,
	PRIMARY KEY ("id")
);

-- Dumping data for table public.layanan: -1 rows
DELETE FROM "layanan";
/*!40000 ALTER TABLE "layanan" DISABLE KEYS */;
INSERT INTO "layanan" ("id", "nama_layanan") VALUES
	(1, 'ADMISI'),
	(2, 'IGD'),
	(3, 'LABORATORIUM'),
	(4, 'FARMASI'),
	(5, 'RAWAT INAP'),
	(6, 'OPERASI'),
	(7, 'RAWAT JALAN'),
	(8, 'RADIOLOGI'),
	(9, 'ICU'),
	(10, 'GIZI');
/*!40000 ALTER TABLE "layanan" ENABLE KEYS */;

-- Dumping structure for table public.nilai
DROP TABLE IF EXISTS "nilai";
CREATE TABLE IF NOT EXISTS "nilai" (
	"id" INTEGER NOT NULL,
	"keterangan" INTEGER NULL DEFAULT NULL,
	PRIMARY KEY ("id")
);

-- Dumping data for table public.nilai: -1 rows
DELETE FROM "nilai";
/*!40000 ALTER TABLE "nilai" DISABLE KEYS */;
INSERT INTO "nilai" ("id", "keterangan") VALUES
	(1, 1),
	(2, 2),
	(3, 3),
	(4, 4);
/*!40000 ALTER TABLE "nilai" ENABLE KEYS */;

-- Dumping structure for table public.profil
DROP TABLE IF EXISTS "profil";
CREATE TABLE IF NOT EXISTS "profil" (
	"id" BIGINT NOT NULL,
	"jenis_kelamin" SMALLINT NOT NULL DEFAULT 0,
	"pekerjaan" VARCHAR(255) NOT NULL,
	"pendidikan" VARCHAR(50) NOT NULL,
	"asuransi_id" INTEGER NOT NULL,
	"layanan_id" INTEGER NOT NULL,
	PRIMARY KEY ("id"),
	CONSTRAINT "fk_profil_asuransi" FOREIGN KEY ("asuransi_id") REFERENCES "asuransi" ("id") ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT "fk_profil_layanan" FOREIGN KEY ("layanan_id") REFERENCES "layanan" ("id") ON UPDATE NO ACTION ON DELETE NO ACTION
);

-- Dumping data for table public.profil: -1 rows
DELETE FROM "profil";
/*!40000 ALTER TABLE "profil" DISABLE KEYS */;
/*!40000 ALTER TABLE "profil" ENABLE KEYS */;

-- Dumping structure for table public.responden
DROP TABLE IF EXISTS "responden";
CREATE TABLE IF NOT EXISTS "responden" (
	"id" INTEGER NOT NULL,
	"pertanyaan" TEXT NULL DEFAULT NULL,
	PRIMARY KEY ("id")
);

-- Dumping data for table public.responden: -1 rows
DELETE FROM "responden";
/*!40000 ALTER TABLE "responden" DISABLE KEYS */;
INSERT INTO "responden" ("id", "pertanyaan") VALUES
	(2, 'Bagaimana pendapat saudara tentang kesesuaian persyaratan pelayanan dengan jenis pelayanannya?'),
	(3, 'Bagaimana pemahaman Anda tentang kemudahan prosedur pelayanan di unit ini?'),
	(4, 'Bagaimana pendapat Anda tentang kecepatan waktu dalam memberikan pelayanan?'),
	(5, 'Bagaimana pendapat Anda tentang kewajaran biaya/tarif dalam pelayanan? (Jika peserta BPJS/Asuransi tidak perlu diisi)'),
	(6, 'Bagaimana pendapat Anda tentang kesesuaian produk pelayanan antara yang tercantum dalam standar pelayanan dengan hasil yang diberikan?'),
	(7, 'Bagaimana pendapat Anda tentang kompetensi/kemampuan petugas dalam pelayanan?'),
	(8, 'Bagaimana pendapat Anda tentang perilaku petugas dalam pelayanan terkait kesopanan dan keramahan?'),
	(9, 'Bagaimana pendapat Anda tentang penanganan pengaduan pengguna layanan?'),
	(10, 'Bagaimana pendapat Anda tentang kualitas sarana dan prasarana?');
/*!40000 ALTER TABLE "responden" ENABLE KEYS */;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;


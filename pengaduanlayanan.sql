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
CREATE TABLE IF NOT EXISTS "asuransi" (
	"id" INTEGER NOT NULL,
	"nama_asuransi" VARCHAR NULL DEFAULT NULL,
	PRIMARY KEY ("id")
);

-- Dumping data for table public.asuransi: 2 rows
DELETE FROM "asuransi";
/*!40000 ALTER TABLE "asuransi" DISABLE KEYS */;
INSERT INTO "asuransi" ("id", "nama_asuransi") VALUES
	(1, 'BPJS'),
	(2, 'UMUM');
/*!40000 ALTER TABLE "asuransi" ENABLE KEYS */;

-- Dumping structure for table public.formulir
CREATE TABLE IF NOT EXISTS "formulir" (
	"id" INTEGER NOT NULL,
	"alamat" VARCHAR NULL DEFAULT NULL,
	"no_hp" BIGINT NULL DEFAULT NULL,
	"masukan" VARCHAR NULL DEFAULT NULL,
	"created_at" TIMESTAMP NULL DEFAULT NULL,
	"tanggal" DATE NULL DEFAULT NULL,
	"profil_id" INTEGER NULL DEFAULT NULL,
	PRIMARY KEY ("id"),
	CONSTRAINT "FK_formulir_profil" FOREIGN KEY ("profil_id") REFERENCES "profil" ("id") ON UPDATE NO ACTION ON DELETE NO ACTION
);

-- Dumping data for table public.formulir: 0 rows
DELETE FROM "formulir";
/*!40000 ALTER TABLE "formulir" DISABLE KEYS */;
/*!40000 ALTER TABLE "formulir" ENABLE KEYS */;

-- Dumping structure for table public.layanan
CREATE TABLE IF NOT EXISTS "layanan" (
	"id" INTEGER NOT NULL,
	"nama_layanan" VARCHAR NULL DEFAULT NULL,
	PRIMARY KEY ("id")
);

-- Dumping data for table public.layanan: 10 rows
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
CREATE TABLE IF NOT EXISTS "profil" (
	"id" INTEGER NOT NULL,
	"jenis_kelamin" SMALLINT NOT NULL DEFAULT '0',
	"pekerjaan" VARCHAR NOT NULL,
	"pendidikan" VARCHAR NOT NULL,
	"asuransi_id" INTEGER NOT NULL,
	"layanan_id" INTEGER NOT NULL,
	PRIMARY KEY ("id")
);

-- Dumping data for table public.profil: -1 rows
DELETE FROM "profil";
/*!40000 ALTER TABLE "profil" DISABLE KEYS */;
/*!40000 ALTER TABLE "profil" ENABLE KEYS */;

-- Dumping structure for table public.responden
CREATE TABLE IF NOT EXISTS "responden" (
	"id" INTEGER NOT NULL,
	"pertanyaan" TEXT NULL DEFAULT NULL,
	"nilai_id" INTEGER NULL DEFAULT NULL,
	"profil_id" INTEGER NULL DEFAULT NULL,
	PRIMARY KEY ("id"),
	CONSTRAINT "FK_responden_nilai" FOREIGN KEY ("nilai_id") REFERENCES "nilai" ("id") ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT "FK_responden_profil" FOREIGN KEY ("profil_id") REFERENCES "profil" ("id") ON UPDATE NO ACTION ON DELETE NO ACTION
);

-- Dumping data for table public.responden: 9 rows
DELETE FROM "responden";
/*!40000 ALTER TABLE "responden" DISABLE KEYS */;
INSERT INTO "responden" ("id", "pertanyaan", "nilai_id", "profil_id") VALUES
	(2, 'Bagaimana pendapat saudara tentang Bagaimana pendapat saudara tentang kesesuaian persyaratan pelayanan dengan jenis pelayanannya?', NULL, NULL),
	(3, 'Bagaimana pemahaman saudara tentang kemudahan pelayanan prosedur pelayanan di unit ini?', NULL, NULL),
	(4, 'Bagaimana pendapat saudara tentang kecepatan waktu dalam memberikan pelayanan?', NULL, NULL),
	(5, 'Bagaimana pendapat saudara tentang kewajaran biaya atau tarif dalam pelayanan?
(Jika saudara peserta BPJS/Asuransi tidak perlu diisi)', NULL, NULL),
	(6, 'Bagaimana pendapat saudara tentang kesesuaian produk pelayanan antara yang tercantum dalam standar pelayanan dengan hasil yang diberikan?', NULL, NULL),
	(7, 'Bagaimana pendapat saudara tentang kompetensi/kemampuan petugas dalam pelayanan?', NULL, NULL),
	(8, 'Bagaimana pendapat saudara tentang perilaku petugas dalam pelayanan terkait kesopanan dan keramahan?', NULL, NULL),
	(9, 'Bagaimana pendapat saudara tentang penanganan pengaduan pengguna layanan?', NULL, NULL),
	(10, 'Bagaimana pendapat saudara tentang kualitas sarana dan prasarana?', NULL, NULL);
/*!40000 ALTER TABLE "responden" ENABLE KEYS */;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;

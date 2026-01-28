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
	"nama_asuransi" VARCHAR(50) NULL DEFAULT NULL,
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

-- Dumping data for table public.formulir: 0 rows
DELETE FROM "formulir";
/*!40000 ALTER TABLE "formulir" DISABLE KEYS */;
/*!40000 ALTER TABLE "formulir" ENABLE KEYS */;

-- Dumping structure for table public.kuesioner
CREATE TABLE IF NOT EXISTS "kuesioner" (
	"id" BIGINT NOT NULL,
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
	"q4" SMALLINT NOT NULL,
	"q5" SMALLINT NOT NULL,
	"q6" SMALLINT NOT NULL,
	"q7" SMALLINT NOT NULL,
	"q8" SMALLINT NOT NULL,
	"q9" SMALLINT NOT NULL,
	"nama_pasien" VARCHAR(150) NOT NULL,
	"alamat" TEXT NOT NULL,
	"nomor_hp" VARCHAR(30) NOT NULL,
	"keluhan" TEXT NULL DEFAULT NULL,
	PRIMARY KEY ("id"),
	CONSTRAINT "chk_q1" CHECK (((q1 >= 1) AND (q1 <= 4))),
	CONSTRAINT "chk_q2" CHECK (((q2 >= 1) AND (q2 <= 4))),
	CONSTRAINT "chk_q3" CHECK (((q3 >= 1) AND (q3 <= 4))),
	CONSTRAINT "chk_q4" CHECK (((q4 >= 1) AND (q4 <= 4))),
	CONSTRAINT "chk_q5" CHECK (((q5 >= 1) AND (q5 <= 4))),
	CONSTRAINT "chk_q6" CHECK (((q6 >= 1) AND (q6 <= 4))),
	CONSTRAINT "chk_q7" CHECK (((q7 >= 1) AND (q7 <= 4))),
	CONSTRAINT "chk_q8" CHECK (((q8 >= 1) AND (q8 <= 4))),
	CONSTRAINT "chk_q9" CHECK (((q9 >= 1) AND (q9 <= 4)))
);

-- Dumping data for table public.kuesioner: 12 rows
DELETE FROM "kuesioner";
/*!40000 ALTER TABLE "kuesioner" DISABLE KEYS */;
INSERT INTO "kuesioner" ("id", "created_at", "survey_date", "survey_time", "gender", "education", "jobs", "services", "q1", "q2", "q3", "q4", "q5", "q6", "q7", "q8", "q9", "nama_pasien", "alamat", "nomor_hp", "keluhan") VALUES
	(1, '2026-01-28 08:23:05.114995', '2026-01-28', '08-12', 'male', 's1', 'pelajar', 'admissi', 3, 4, 3, 3, 3, 4, 3, 3, 4, 'Rio Roi', 'Jl. Menganti', '08123123123', 'Kurang ramah adqwdqfnqwfqw njasncjwvbejbfqw vjwnfjqnfjn ndiqwfhiquwv nqowdjoqiwnf sjdnjqwifhq'),
	(2, '2026-01-28 10:09:44.123517', '2026-01-28', '08-12', 'male', 's1', 'wirausaha,pelajar', 'admissi', 4, 1, 2, 4, 3, 3, 2, 1, 4, 'Bahenol', 'Jakarta, Jl. Kemanggisan 34', '082112346758', 'wifi lemot, lift rusak, lantai licin, ac mati, pegawai jelek, tidak ramah, toxic, hitam, gigi kuning'),
	(3, '2026-01-28 10:18:45.06348', '2026-01-20', '08-12', 'male', 'SMA', 'Karyawan Swasta', 'Pendaftaran', 4, 4, 3, 4, 3, 4, 4, 3, 4, 'Ahmad Fauzi', 'Jl. Merdeka No. 12', '081234567890', 'Pelayanan sudah cukup baik'),
	(4, '2026-01-28 10:18:45.06348', '2026-01-20', '08-12', 'female', 'S1', 'Ibu Rumah Tangga', 'Poli Umum', 3, 3, 3, 2, 3, 3, 3, 2, 3, 'Siti Aminah', 'Jl. Kenanga No. 5', '081298765432', 'Antrian agak lama'),
	(6, '2026-01-28 10:18:45.06348', '2026-01-21', '08-12', 'female', 'SMA', 'Mahasiswa', 'Apotek', 3, 4, 3, 3, 4, 3, 3, 4, 3, 'Rina Lestari', 'Jl. Mawar Indah', '085677889900', 'Obat tersedia lengkap'),
	(8, '2026-01-28 10:18:45.06348', '2026-01-22', '08-12', 'female', 'S1', 'Guru', 'Poli Anak', 4, 4, 4, 4, 4, 4, 4, 4, 4, 'Nur Aisyah', 'Jl. Melati No. 7', '082233344455', 'Pelayanan cepat dan jelas'),
	(10, '2026-01-28 10:18:45.06348', '2026-01-22', '12-18', 'female', 'D3', 'Perawat', 'Pendaftaran', 2, 3, 3, 2, 3, 3, 2, 3, 3, 'Dewi Anggraini', 'Komplek Cendana', '083344455566', 'Sistem antrian perlu diperbaiki'),
	(12, '2026-01-28 10:18:45.06348', '2026-01-23', '12-18', 'female', 'SMA', 'Pedagang', 'Apotek', 3, 3, 3, 3, 3, 3, 3, 3, 3, 'Lina Marlina', 'Jl. Anggrek No. 9', '085266677788', 'Pelayanan sudah memuaskan'),
	(5, '2026-01-28 10:18:45.06348', '2026-01-21', '12-18', 'male', 'D3', 'Wiraswasta', 'Laboratorium', 4, 4, 4, 4, 4, 4, 4, 4, 4, 'Budi Santoso', 'Perum Griya Sejahtera', '082112223333', 'Petugas ramah dan informatif'),
	(7, '2026-01-28 10:18:45.06348', '2026-01-21', '12-18', 'male', 'SMP', 'Petani', 'Poli Gigi', 2, 3, 2, 3, 2, 3, 2, 3, 2, 'Dedi Kurniawan', 'Desa Sukamaju', '081377788899', 'Ruang tunggu kurang nyaman'),
	(9, '2026-01-28 10:18:45.06348', '2026-01-22', '08-12', 'male', 'SMA', 'Supir', 'IGD', 3, 3, 4, 4, 3, 4, 3, 4, 3, 'Agus Salim', 'Jl. Raya Timur', '081999888777', 'Petugas sigap'),
	(11, '2026-01-28 10:18:45.06348', '2026-01-23', '08-12', 'male', 'S1', 'PNS', 'Poli Penyakit Dalam', 4, 4, 4, 3, 4, 4, 4, 3, 4, 'Hendra Wijaya', 'Jl. Veteran', '081122334455', 'Dokter komunikatif');
/*!40000 ALTER TABLE "kuesioner" ENABLE KEYS */;

-- Dumping structure for table public.layanan
CREATE TABLE IF NOT EXISTS "layanan" (
	"id" INTEGER NOT NULL,
	"nama_layanan" VARCHAR(100) NULL DEFAULT NULL,
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

-- Dumping data for table public.nilai: 4 rows
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

-- Dumping data for table public.profil: 0 rows
DELETE FROM "profil";
/*!40000 ALTER TABLE "profil" DISABLE KEYS */;
/*!40000 ALTER TABLE "profil" ENABLE KEYS */;

-- Dumping structure for table public.responden
CREATE TABLE IF NOT EXISTS "responden" (
	"id" INTEGER NOT NULL,
	"pertanyaan" TEXT NULL DEFAULT NULL,
	PRIMARY KEY ("id")
);

-- Dumping data for table public.responden: 9 rows
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

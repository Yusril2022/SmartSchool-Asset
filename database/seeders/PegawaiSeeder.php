<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PegawaiSeeder extends Seeder
{
    public function run(): void
    {
        $pegawais = [
            ['nama' => 'Hj Rizky Aviatin, S.Pd., M.Pd.'],
            ['nama' => 'Tresa Agustian, S.Kom., M.M.'],
            ['nama' => 'Lia Yulianti, S.Pd., M.Pd.'],
            ['nama' => 'Rini Setyawati, S.Pd.'],
            ['nama' => 'Iis Nur Aisyah, S.T.'],
            ['nama' => 'Drs. Yayan Supwakhyan, M.Pd.'],
            ['nama' => 'Erik Perdana Ibrahim, S.Kom., M.M.'],
            ['nama' => 'Danny Erwansyah, S.I.P., S.Pd., M.Pd.'],
            ['nama' => 'Tatang Supriatna, S.Kom., M.M.'],
            ['nama' => 'Enny Setyowati, S.T., S.Pd.'],
            ['nama' => 'Novi Sofia Kahirani, S.Si.'],
            ['nama' => 'Ira Puspita, S.Pd.'],
            ['nama' => 'Maryati, S.T.'],
            ['nama' => 'Nurhasanah, S.Pd.'],
            ['nama' => 'Kamala Devi, S.Pd.'],
            ['nama' => 'Puspa Dewi, S.Pd.'],
            ['nama' => 'Riki Lesmana, S.Pd.'],
            ['nama' => 'Trisna Handayani, S.Pd.'],
            ['nama' => 'Kiki Puji Astuti, S.Pd.'],
            ['nama' => 'Grandis Agung Ayodyawan, S.Pd.'],
            ['nama' => 'Nisrina Nastiti Besar, S.Pd.'],
            ['nama' => 'Amalia Aprilda Subandi, S.Pd.'],
            ['nama' => 'Kamala Mustika Dewi, S.Pd.'],
            ['nama' => 'Lia Elivia Febrianti, S.Pd.'],
            ['nama' => 'Elin Ratna Yulia, S.Sn.'],
            ['nama' => 'Wayim, S.Pd.I., M.A.'],
            ['nama' => 'Anwar Suhendar, S.Sos.I. M.M.'],
            ['nama' => 'Purwanti Leni Triana, S.Pd.I.'],
            ['nama' => 'Suherti, S.Pd.'],
            ['nama' => 'Dita Eka Wulandari, S.Pd.'],
            ['nama' => 'Sitta Nur Azizah Rizqoh, S.S.'],
            ['nama' => 'Cucun Cunayah, S.H., M.H.'],
            ['nama' => 'Siti Holisoh, A.Md.'],
            ['nama' => 'Sari Handayani, S.Pd.'],
            ['nama' => 'Soniawati, S.Pd.I.'],
            ['nama' => 'Gr. R. Mohamad Nurrizal Anhar, S.Pd.'],
            ['nama' => 'Dea Dwiartini, S.Pd.'],
            ['nama' => 'Muhamad Ridwan, S.Pd.'],
            ['nama' => 'Gr. Arif Rangga Mulyana, S.Pd.'],
            ['nama' => 'Gr. Tantri Yulianti, S.Pd.'],
            ['nama' => 'Sunariah Hartini, S.Pd.'],
            ['nama' => 'Muhammad Miftahul Khoir, S.Pd.'],
            ['nama' => 'Mira Piyanti, S.Pd.'],
            ['nama' => 'Hamzah Arfianto, S.Pd.'],
            ['nama' => 'Muhammad Rully Syaepudin, S.Pd.'],
            ['nama' => 'Fitri Nisa, S.Hum.'],
            ['nama' => 'Novian Rizky, S.Pd.'],
            ['nama' => 'Retno Wulandari, S.I.P.'],
            ['nama' => 'Saepudin, S.Kom.'],
            ['nama' => 'Rina Farida, S.E.'],
            ['nama' => 'Bayu Sakti Bahar'],
            ['nama' => 'Agil Anwar Musaddad, A.Md.Kom'],
            ['nama' => 'Nandya Rizqie Pramudibyo, S.Kom.'],
            ['nama' => 'Imam Gozali'],
            ['nama' => 'Mas Asep'],
            ['nama' => 'Aman Sutapraja'],
            ['nama' => 'Yudo Baskoro'],
            ['nama' => 'Leni Marlina'],
            ['nama' => 'Endang Kurniawan'],
            ['nama' => 'Hendrik Setiawan'],
        ];

        foreach ($pegawais as $pegawai) {
            DB::table('pegawais')->insert([
                'nama'       => $pegawai['nama'],
                'aktif'      => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
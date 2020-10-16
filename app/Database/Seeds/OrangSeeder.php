<?php

namespace App\Database\Seeds;

use CodeIgniter\I18n\Time;

class OrangSeeder extends \CodeIgniter\Database\Seeder
{
    public function run()
    {
        // $data = [
        //     [
        //         'nama' => 'Felia Citra',
        //         'alamat'    => 'Slarang Lor',
        //         'created_at' => time::now(),
        //         'updated_at' => time::now()
        //     ],
        //     [
        //         'nama' => 'Dwiyani',
        //         'alamat'    => 'Slarang Kidul',
        //         'created_at' => time::now(),
        //         'updated_at' => time::now()
        //     ]

        // ];
        $faker = \Faker\Factory::create('id_ID');
        // dd($faker->address);
        for ($i = 0; $i < 20; $i++) {
            $data = [

                'nama' => $faker->name,
                'alamat'    => $faker->address,
                'created_at' => time::createFromTimestamp($faker->unixTime()),
                'updated_at' => time::now()

            ];
            $this->db->table('orang')->insert($data);
        }


        // Simple Queries
        // $this->db->query(
        //     "INSERT INTO orang (nama, alamat, created_at, updated_at) VALUES(:nama:, :alamat:, :created_at:, :updated_at:)",
        //     $data
        // );

        // Using Query Builder
        // $this->db->table('orang')->insertBatch($data);
    }
}

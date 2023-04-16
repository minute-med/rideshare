<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('vehicle_brands')->truncate();
        DB::table('vehicle_brand_models')->truncate();
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://parseapi.back4app.com/classes/Car_Model_List?limit=10000');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'X-Parse-Application-Id: hlhoNKjOvEhqzcVAJ1lxjicJLZNVv36GdbboZj3Z', // This is the fake app's application id
            'X-Parse-Master-Key: SNMJJF0CZZhTPhLDIqGhTlUNV9r60M2Z5spyWfXW' // This is the fake app's readonly master key
        ));
        $data = json_decode(curl_exec($curl), true);
        curl_close($curl);

        $cars = [];
        
        foreach ($data['results'] as $val) {
            $cats = explode(',', $val['Category']);
            if(count($cats) > 1) {
                // var_dump($cats);die;
                $cars[$val['Make']][$val['Model']][$cats[0]][$val['Year']] = 1;
                $cars[$val['Make']][$val['Model']][$cats[1]][$val['Year']] = 1;

            } else {
                $cars[$val['Make']][$val['Model']][$val['Category']][$val['Year']] = 1;
            }
        }

        foreach($cars as $brand => $models) {
            $brand_id = DB::table('vehicle_brands')->insert([
                'name' => strtolower($brand)
            ]);

            foreach ($models as $model => $category) {
                $inserted_id = DB::table('vehicle_models')->insert([
                    'vehicle_brand_id' => strtolower($brand_id),
                ]); 
            }
        }

    }
}

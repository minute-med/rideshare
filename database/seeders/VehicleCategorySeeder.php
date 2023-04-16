<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use App\Models\VehicleCategory;

class VehicleCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('vehicle_categories')->truncate();
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
            $i = 0;
            $cats = explode(',', $val['Category']);
            while ($i < count($cats)) {
                if(preg_match('/[0-9]+/', $cats[$i]) !== false) {
                    $clean = preg_replace('/[0-9]+/', '', $cats[$i]);
                    VehicleCategory::updateOrCreate(['name' => strtolower($clean)]);
                }
                $i++;
            }
        }
        Schema::enableForeignKeyConstraints();
    }
}

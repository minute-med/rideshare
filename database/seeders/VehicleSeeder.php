<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use App\Models\VehicleModel;
use App\Models\VehicleBrand;
use App\Models\VehicleCategory;


class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::withoutForeignKeyConstraints(function () {
            DB::table('vehicle_brands')->truncate();
            DB::table('vehicle_models')->truncate();
            DB::table('vehicle_categories')->truncate();
            DB::table('model_category')->truncate();
            
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, 'https://parseapi.back4app.com/classes/Car_Model_List?limit=10000');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'X-Parse-Application-Id: hlhoNKjOvEhqzcVAJ1lxjicJLZNVv36GdbboZj3Z', // This is the fake app's application id
                'X-Parse-Master-Key: SNMJJF0CZZhTPhLDIqGhTlUNV9r60M2Z5spyWfXW' // This is the fake app's readonly master key
            ));
            $data = json_decode(curl_exec($curl), true);
            curl_close($curl);
    

            foreach ($data['results'] as $val) {

                $model = $val['Model'];
                $year = $val['Year'];
                $brand = strtolower($val['Make']);
                $categories = explode(',', $val['Category']);

                $categoriesDB = array_map(function($cat){
                    return ['name' => trim(strtolower(preg_replace('/[0-9]+/', '', $cat)))];
                }, $categories);
                
                DB::table('vehicle_brands')->upsert(
                    ['name' => $brand],
                    ['name'],
                    ['name']
                );

                DB::table('vehicle_categories')->upsert(
                    $categoriesDB,
                    ['name'],
                    ['name']
                );

                $vehicle_brand = VehicleBrand::where('name', $brand)->first();

                $cats = array_map(function($cat){
                    return trim(strtolower(preg_replace('/[0-9]+/', '', $cat)));
                }, $categories);

                $model = VehicleModel::firstOrCreate([
                    'name' => $model,
                    'vehicle_brand_id' => $vehicle_brand->id,
                ]);

                $catsIDS = VehicleCategory::select('id')
                ->whereIn('name', $cats)
                ->get();

                foreach($catsIDS as $cid) {
                    if(!$model->categories->contains($cid)) {
                        $model->categories()->attach($cid);
                    }
                }
            }
        });
    }
}

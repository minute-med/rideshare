<?php
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'https://parseapi.back4app.com/classes/Car_Model_List?limit=10000');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'X-Parse-Application-Id: hlhoNKjOvEhqzcVAJ1lxjicJLZNVv36GdbboZj3Z', // This is the fake app's application id
        'X-Parse-Master-Key: SNMJJF0CZZhTPhLDIqGhTlUNV9r60M2Z5spyWfXW' // This is the fake app's readonly master key
    ));
    $data = json_decode(curl_exec($curl), true); // Here you have the data that you need
    // print_r(json_encode($data, JSON_PRETTY_PRINT));
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
    
    /*[
    {
      "objectId": "ZRgPP9dBMm",
      "Year": 2020,
      "Make": "Audi",
      "Model": "Q3",
      "Category": "SUV",
      "createdAt": "2020-01-27T20:44:17.665Z",
      "updatedAt": "2020-01-27T20:44:17.665Z"
    },
    ]
     */
?>
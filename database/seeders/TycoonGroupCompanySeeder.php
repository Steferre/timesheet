<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TycoonGroupCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        date_default_timezone_set('Europe/Rome');
        $date = date('Y/m/d H:i:s');
         
        $companies = [
            [
               'businessName' => 'Della Monica & Partners stp', 
               'email' => 'consulenza@dellamonica.it', 
               'emailPEC' => 'stp@pec.dellamonica.it', 
               'pIva' => '08685250964', 
               'address' => 'Via Vitruvio', 
               'buldingNum' => '38', 
               'city' => 'Milano', 
               'province' => 'MI', 
               'country' => 'Italia', 
               'postalCode' => '20124', 
               'phone' => '02868831', 
               'fax' => '0267382659', 
               'website' => 'https://dellamonica.it/', 
            ],
            [
                'businessName' => 'Delmoform srl', 
                'email' => 'formazione@delmoform.it', 
                'emailPEC' => 'direzione@pec.delmoform.it ', 
                'pIva' => '05213410961', 
                'address' => 'Via Carlo Tenca', 
                'buldingNum' => '15', 
                'city' => 'Milano', 
                'province' => 'MI', 
                'country' => 'Italia', 
                'postalCode' => '20124', 
                'phone' => '0266980923', 
                'fax' => '0240055250', 
                'website' => 'https://delmoform.it/', 
            ],
            [
                'businessName' => 'Frama Development Srl', 
                'email' => 'info@framadev.it', 
                'emailPEC' => 'commerciale@framadev.it', 
                'pIva' => '02878380969', 
                'address' => 'Via Domenichino', 
                'buldingNum' => '49', 
                'city' => 'Milano', 
                'province' => 'MI', 
                'country' => 'Italia', 
                'postalCode' => '20129', 
                'phone' => '029528554', 
                'fax' => '0295341309', 
                'website' => 'https://www.framadev.it/', 
            ],
            [
                'businessName' => 'Giuseppe Fiorani', 
                'email' => '', 
                'emailPEC' => '', 
                'pIva' => '11644730159', 
                'address' => 'Via G. Avezzana', 
                'buldingNum' => '1', 
                'city' => 'Milano', 
                'province' => 'MI', 
                'country' => 'Italia', 
                'postalCode' => '20139', 
                'phone' => '0245494058', 
                'fax' => '', 
                'website' => 'https://giuseppefiorani.it/', 
            ],
            [
                'businessName' => 'IMC Studio', 
                'email' => 'manuel.clemente@imcstudio.it', 
                'emailPEC' => '', 
                'pIva' => '08194760966', 
                'address' => 'Via XXIV Maggio', 
                'buldingNum' => '8', 
                'city' => 'Busto Garolfo', 
                'province' => 'MI', 
                'country' => 'Italia', 
                'postalCode' => '20038', 
                'phone' => '3338234544', 
                'fax' => '', 
                'website' => 'https://imcstudio.it/', 
            ],
            [
                'businessName' => 'KeyOS srl', 
                'email' => 'info@keyos.it', 
                'emailPEC' => 'info@pec.keyos.it', 
                'pIva' => 'IT08443710960', 
                'address' => 'Via San Gregorio', 
                'buldingNum' => '40', 
                'city' => 'Milano', 
                'province' => 'MI', 
                'country' => 'Italia', 
                'postalCode' => '20124', 
                'phone' => '0286883150', 
                'fax' => '', 
                'website' => 'https://www.keyos.it/', 
            ],
            [
                'businessName' => 'OneSeal s.r.l', 
                'email' => 'gdpr@oneseal.eu', 
                'emailPEC' => '', 
                'pIva' => '10115390964', 
                'address' => 'Via Vitruvio', 
                'buldingNum' => '38', 
                'city' => 'Milano', 
                'province' => 'MI', 
                'country' => 'Italia', 
                'postalCode' => '20124', 
                'phone' => '0286883190', 
                'fax' => '', 
                'website' => 'https://www.oneseal.eu/', 
            ],
            [
                'businessName' => 'Progetto HR S.r.l.', 
                'email' => '', 
                'emailPEC' => 'amministrazione@pec.progettohr.com', 
                'pIva' => '09450130969', 
                'address' => 'Via Vitruvio', 
                'buldingNum' => '38', 
                'city' => 'Milano', 
                'province' => 'MI', 
                'country' => 'Italia', 
                'postalCode' => '20124', 
                'phone' => '0221079680', 
                'fax' => '', 
                'website' => 'https://www.progettohr.com/', 
            ],
            [
                'businessName' => 'Sys UP SRL', 
                'email' => 'info@sysup.it', 
                'emailPEC' => '', 
                'pIva' => '11035670964', 
                'address' => 'Via Vitruvio', 
                'buldingNum' => '38', 
                'city' => 'Milano', 
                'province' => 'MI', 
                'country' => 'Italia', 
                'postalCode' => '20124', 
                'phone' => '0286883180', 
                'fax' => '', 
                'website' => 'https://www.sysup.it/', 
            ],
        ];

        foreach ($companies as $company) {
            DB::table('tycoon_group_companies')->insert([
                'businessName' => $company['businessName'],
                'email' => $company['email'],
                'emailPEC' => $company['emailPEC'],
                'pIva' => $company['pIva'],
                'address' => $company['address'],
                'buldingNum' => $company['buldingNum'],
                'city' => $company['city'],
                'province' => $company['province'],
                'country' => $company['country'],
                'postalCode' => $company['postalCode'],
                'phone' => $company['phone'],
                'fax' => $company['fax'],
                'website' => $company['website'],
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }
    }
}

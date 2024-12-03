<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Specialization;


class SpecializationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specializationsList = [
            "Medicina interna",
            "Medicina d'emergenza-urgenza",
            "Geriatria",
            "Medicina dello sport e dell'esercizio fisico",
            "Medicina termale",
            "Oncologia medica",
            "Medicina di comunità e delle cure primarie",
            "Allergologia ed immunologia clinica",
            "Dermatologia e venereologia",
            "Ematologia",
            "Endocrinologia e malattie del metabolismo",
            "Scienza dell'alimentazione",
            "Malattie dell'apparato digerente",
            "Malattie dell'apparato cardiovascolare",
            "Malattie dell'apparato respiratorio",
            "Malattie infettive e tropicali",
            "Nefrologia",
            "Reumatologia",
            "Neurologia",
            "Neuropsichiatria infantile",
            "Psichiatria",
            "Pediatria",
            "Chirurgia generale",
            "Chirurgia pediatrica",
            "Chirurgia plastica, ricostruttiva ed estetica",
            "Ginecologia ed ostetricia",
            "Ortopedia e traumatologia",
            "Urologia",
            "Chirurgia maxillo-facciale",
            "Neurochirurgia",
            "Oftalmologia",
            "Otorinolaringoiatria",
            "Cardiochirurgia",
            "Chirurgia toracica",
            "Chirurgia vascolare",
            "Anatomia patologica",
            "Microbiologia e virologia",
            "Patologia clinica e biochimica clinica",
            "Radiodiagnostica",
            "Radioterapia",
            "Medicina nucleare",
            "Anestesia, rianimazione e terapia intensiva e del dolore",
            "Audiologia e foniatria",
            "Medicina fisica e riabilitativa",
            "Farmacologia e tossicologia clinica",
            "Genetica medica",
            "Igiene e medicina preventiva",
            "Medicina del lavoro",
            "Medicina legale",
            "Statistica sanitaria e biometria"
        ];

        foreach($specializationsList as $specialization) {
            $newSpecialization = new Specialization();
            $newSpecialization->name = $specialization;
            $newSpecialization->save();
        }
    }
}

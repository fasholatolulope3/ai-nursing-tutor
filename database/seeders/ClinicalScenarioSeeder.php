<?php

namespace Database\Seeders;

use App\Models\ClinicalScenario;
use Illuminate\Database\Seeder;

class ClinicalScenarioSeeder extends Seeder
{
    public function run(): void
    {
        ClinicalScenario::create([
            'title' => 'Sepsis Management',
            'description' => 'A 65-year-old male presents with fever, hypotension, and altered mental status. Identify and manage sepsis early.',
            'objective' => [
                'Recognize signs of sepsis.',
                'Initiate fluid resuscitation.',
                'Administer antibiotics within 1 hour.',
            ],
            'complexity' => 'intermediate',
            'initial_patient_state' => [
                'hr' => 110,
                'bp' => '90/50',
                'temp' => 39.2,
                'rr' => 24,
                'sao2' => 94,
                'mental_status' => 'confused',
            ],
            'medical_history' => [
                'Hypertension',
                'Type 2 Diabetes',
            ],
        ]);

        ClinicalScenario::create([
            'title' => 'Acute Myocardial Infarction',
            'description' => 'A 50-year-old female complaining of crushing chest pain and shortness of breath.',
            'objective' => [
                'Perform 12-lead ECG.',
                'Administer aspirin and nitroglycerin.',
                'Assess pain and vitals.',
            ],
            'complexity' => 'advanced',
            'initial_patient_state' => [
                'hr' => 98,
                'bp' => '150/90',
                'temp' => 37.0,
                'rr' => 20,
                'sao2' => 96,
                'pain' => '8/10',
            ],
            'medical_history' => [
                'Smoker',
                'Hyperlipidemia',
            ],
        ]);
    }
}

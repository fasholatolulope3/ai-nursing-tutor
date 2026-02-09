<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReferenceDocument;

class ReferenceDocumentSeeder extends Seeder
{
    public function run(): void
    {
        $docs = [
            [
                'title' => 'ACL Protocol',
                'description' => 'Advanced Cardiovascular Life Support guidelines and medication dosages.',
                'file_path' => '/documents/acl_protocol.pdf',
                'file_type' => 'pdf',
                'tags' => ['cardiology', 'emergency', 'acls']
            ],
            [
                'title' => 'Ethics & Boundaries',
                'description' => 'Nursing ethical guidelines for professional practice and patient care.',
                'file_path' => '/documents/ethics.pdf',
                'file_type' => 'pdf',
                'tags' => ['ethics', 'professionalism', 'legal']
            ],
            [
                'title' => 'Sepsis Screening',
                'description' => 'Standardized protocol for early detection and management of sepsis.',
                'file_path' => '/documents/sepsis_screening.pdf',
                'file_type' => 'pdf',
                'tags' => ['critical care', 'sepsis', 'infection']
            ],
            [
                'title' => 'Pediatric Dosing',
                'description' => 'Pharmacological guide for pediatric weight-based medication dosages.',
                'file_path' => '/documents/pediatric_dosing.pdf',
                'file_type' => 'pdf',
                'tags' => ['pediatrics', 'medication', 'dosage']
            ],
        ];

        foreach ($docs as $doc) {
            ReferenceDocument::updateOrCreate(
                ['title' => $doc['title']],
                $doc
            );
        }
    }
}

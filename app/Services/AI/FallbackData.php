<?php

namespace App\Services\AI;

class FallbackData
{
    public static function getScenarios()
    {
        return [
            [
                'scenario_briefing' => [
                    'title' => 'Acute Sepsis Management (Offline Fallback)',
                    'story' => 'Mrs. Johnson, a 72-year-old female, is admitted with fever, hypotension, and altered mental status. She has a history of UTI. You are the charge nurse in the ED.',
                    'patient_profile' => [
                        'name' => 'Martha Johnson',
                        'age' => 72,
                        'gender' => 'Female',
                        'allergies' => 'Penicillin',
                        'history' => 'Hypertension, Diabetes Type 2, Recurrent UTIs'
                    ]
                ],
                'learning_objectives' => [
                    'Identify early signs of sepsis.',
                    'Initiate the Sepsis Six bundle within 1 hour.',
                    'Monitor response to fluid resuscitation.'
                ],
                'difficulty' => 'Intermediate',
                'initial_vitals' => [
                    'HR' => 110,
                    'BP' => '88/50',
                    'RR' => 24,
                    'Temp' => 38.9,
                    'SpO2' => 94
                ]
            ],
            [
                'scenario_briefing' => [
                    'title' => 'Post-Op Hemorrhage (Offline Fallback)',
                    'story' => 'Mr. Chen, 45, is 4 hours post-op from a laparoscopic cholecystectomy. He complains of increasing abdominal pain and shoulder tip pain. Parameters are deteriorating.',
                    'patient_profile' => [
                        'name' => 'David Chen',
                        'age' => 45,
                        'gender' => 'Male',
                        'allergies' => 'None',
                        'history' => 'Cholelithiasis'
                    ]
                ],
                'learning_objectives' => [
                    'Recognize signs of internal bleeding.',
                    'Perform focused abdominal assessment.',
                    'Communicate effectively with the surgical team (SBAR).'
                ],
                'difficulty' => 'Advanced',
                'initial_vitals' => [
                    'HR' => 125,
                    'BP' => '90/60',
                    'RR' => 22,
                    'Temp' => 36.5,
                    'SpO2' => 98
                ]
            ],
            [
                'scenario_briefing' => [
                    'title' => 'COPD Exacerbation (Offline Fallback)',
                    'story' => 'Mr. Miller, 68, presents with increased shortness of breath and productive cough. He has a known history of COPD and is currently on home oxygen.',
                    'patient_profile' => [
                        'name' => 'George Miller',
                        'age' => 68,
                        'gender' => 'Male',
                        'allergies' => 'Latex',
                        'history' => 'COPD, Smoker (40 pack years)'
                    ]
                ],
                'learning_objectives' => [
                    'Assess respiratory status and effort.',
                    'Manage oxygen therapy effectively relative to hypoxic drive.',
                    'Administer nebulized bronchodilators properly.'
                ],
                'difficulty' => 'Beginner',
                'initial_vitals' => [
                    'HR' => 92,
                    'BP' => '135/85',
                    'RR' => 28,
                    'Temp' => 37.1,
                    'SpO2' => 88
                ]
            ]
        ];
    }
}

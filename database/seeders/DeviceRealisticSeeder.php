<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Models\Team;
use App\Models\Device;
use App\Models\Category;
use App\Models\Specialty;
use App\Models\FacilityType;
use App\Models\Territory;
use App\Models\RegulatoryClearance;
use App\Models\ReimbursementCode;
use App\Models\DeviceDocument;

class DeviceRealisticSeeder extends Seeder
{
    public function run(): void
    {
        /* =========================
         * 1) Reference data
         * ========================= */
        $categories = collect([
            'Remote Patient Monitoring',
            'Respiratory',
            'Orthopedics',
            'Neurology',
            'Cardiology',
            'Diagnostics',
        ])->map(fn($name) => Category::firstOrCreate(['name' => $name]));

        $specialties = collect([
            'Primary Care', 'Cardiology', 'Endocrinology', 'Pulmonology', 'Orthopedics', 'Neurology'
        ])->map(fn($name) => Specialty::firstOrCreate(['name' => $name]));

        $facilityTypes = collect([
            'Hospital', 'Clinic', 'ASC (Ambulatory Surgery Center)', 'Home Health', 'Skilled Nursing'
        ])->map(fn($name) => FacilityType::firstOrCreate(['name' => $name]));

        $usStates = [
            'Alabama','Alaska','Arizona','Arkansas','California','Colorado','Connecticut','Delaware','Florida','Georgia',
            'Hawaii','Idaho','Illinois','Indiana','Iowa','Kansas','Kentucky','Louisiana','Maine','Maryland',
            'Massachusetts','Michigan','Minnesota','Mississippi','Missouri','Montana','Nebraska','Nevada','New Hampshire','New Jersey',
            'New Mexico','New York','North Carolina','North Dakota','Ohio','Oklahoma','Oregon','Pennsylvania','Rhode Island','South Carolina',
            'South Dakota','Tennessee','Texas','Utah','Vermont','Virginia','Washington','West Virginia','Wisconsin','Wyoming'
        ];
        $territories = collect($usStates)->map(fn($name) => Territory::firstOrCreate(['name' => $name]));

        /* =========================
         * 2) Manufacturer teams
         * ========================= */
        $mActive   = Team::where('name', 'Manufacturer Active Team')->first();
        $mInactive = Team::where('name', 'Manufacturer Inactive Team')->first();

        $fallbackTeams = Team::take(2)->get();
        if (!$mActive)   $mActive   = $fallbackTeams->first();
        if (!$mInactive) $mInactive = $fallbackTeams->skip(1)->first() ?? $mActive;

        /* =========================
         * 3) Normalizers
         * ========================= */
        $normalizeFdaPathway = function (?string $val): string {
            if (!$val) return 'none';
            $key = strtolower(trim($val));
            $map = [
                'none'            => 'none',
                '510(k)'          => '510k',
                '510k'            => '510k',
                'pma'             => 'pma',
                'de novo'         => '510k',
                'denovo'          => '510k',
                'class i exempt'  => 'exempt',
                'class ii exempt' => 'exempt',
                'exempt'          => 'exempt',
            ];
            $canon = $map[$key] ?? $key;
            return in_array($canon, ['none','exempt','510k','pma'], true) ? $canon : 'none';
        };

        $normalizeClearanceType = function (?string $val): ?string {
            if (!$val) return null;
            $key = strtolower(trim($val));
            $map = [
                'fda 510(k)'        => '510k',
                '510(k)'            => '510k',
                '510k'              => '510k',
                'pma'               => 'pma',
                'de novo'           => '510k',
                'denovo'            => '510k',
                'class i exempt'    => 'exempt',
                'class ii exempt'   => 'exempt',
                'exempt'            => 'exempt',
            ];
            $canon = $map[$key] ?? $key;
            return in_array($canon, ['510k','pma','exempt'], true) ? $canon : null;
        };

        $normalizeCodeType = function (?string $val): ?string {
            if (!$val) return null;
            $key = strtoupper(trim($val));
            return in_array($key, ['CPT','HCPCS','DRG','ICD10'], true) ? $key : null;
        };

        /* =========================
         * 4) Device builder
         * ========================= */
        $makeDevice = function (array $d, Team $owner) use ($categories, $specialties, $facilityTypes, $territories, $normalizeFdaPathway, $normalizeClearanceType, $normalizeCodeType) {
            $categoryId = $categories->firstWhere('name', $d['category'])?->id ?? $categories->first()->id;

            $device = Device::updateOrCreate(
                ['slug' => Str::slug($d['name'].'-'.$d['model'])],
                [
                    'company_id'   => $owner->id,
                    'name'         => $d['name'],
                    'category_id'  => $categoryId,
                    'description'  => $d['description'] ?? null,
                    'indications'  => $d['indications'] ?? null,
                    'fda_pathway'  => $normalizeFdaPathway($d['fda_pathway'] ?? null),
                    'status'       => 'listed',   // ✅ force listed
                    'is_published' => true,       // ✅ mark as published
                ]
            );

            $device->specialties()->sync(
                $specialties->shuffle()->take(rand(1,3))->pluck('id')->all()
            );
            $device->facilityTypes()->sync(
                $facilityTypes->shuffle()->take(rand(1,3))->pluck('id')->all()
            );

            if (!empty($d['territories'])) {
                $ids = collect($d['territories'])
                    ->map(fn($state) => $territories->firstWhere('name', $state)?->id)
                    ->filter()->values()->all();
            } else {
                $ids = $territories->shuffle()->take(rand(2,5))->pluck('id')->all();
            }
            $device->territories()->sync($ids);

            if (!empty($d['clearance'])) {
                RegulatoryClearance::updateOrCreate(
                    ['device_id' => $device->id],
                    [
                        'clearance_type' => $normalizeClearanceType($d['clearance']['type'] ?? null),
                        'number'         => $d['clearance']['number'] ?? null,
                        'issue_date'     => !empty($d['clearance']['issue_date']) ? Carbon::parse($d['clearance']['issue_date']) : null,
                        'link'           => $d['clearance']['link'] ?? null,
                    ]
                );
            }

            if (!empty($d['codes']) && is_array($d['codes'])) {
                foreach ($d['codes'] as $rc) {
                    $codeType = $normalizeCodeType($rc['code_type'] ?? $rc['system'] ?? null);
                    if (!$codeType) continue;

                    ReimbursementCode::updateOrCreate(
                        [
                            'device_id' => $device->id,
                            'code_type' => $codeType,
                            'code'      => $rc['code'],
                        ],
                        ['description' => $rc['description'] ?? null]
                    );
                }
            }

            if (!empty($d['documents']) && is_array($d['documents'])) {
                foreach ($d['documents'] as $doc) {
                    DeviceDocument::updateOrCreate(
                        ['device_id' => $device->id, 'type' => $doc['type'], 'path' => $doc['path']],
                        ['original_name' => $doc['original_name'] ?? basename($doc['path'])]
                    );
                }
            }
        };

        /* =========================
         * 5) Devices
         * ========================= */
        $devices = [
            [
                'name'        => 'CardioTrack RPM Hub',
                'model'       => 'CT-200',
                'category'    => 'Remote Patient Monitoring',
                'description' => 'Cellular RPM hub supporting BP, SpO2, weight, and glucose devices.',
                'indications' => 'Chronic disease monitoring (HTN, CHF, COPD, Diabetes).',
                'fda_pathway' => '510(k)',
                'territories' => ['North Carolina','South Carolina','Virginia'],
                'clearance'   => [
                    'type'       => 'FDA 510(k)',
                    'number'     => 'K233245',
                    'issue_date' => '2023-10-15',
                ],
                'codes' => [
                    ['code_type' => 'CPT','code' => '99453','description' => 'RPM device setup'],
                    ['code_type' => 'CPT','code' => '99454','description' => 'RPM device supply'],
                ],
                'documents' => [
                    ['type' => 'brochure','path' => 'devices/cardio-track/brochure.pdf'],
                ],
                'owner' => 'active',
            ],
            [
                'name'        => 'PulmoAir Spirometer',
                'model'       => 'PA-100',
                'category'    => 'Respiratory',
                'description' => 'Portable spirometer for COPD and asthma monitoring.',
                'indications' => 'Pulmonary function trending and RPM.',
                'fda_pathway' => '510(k)',
                'territories' => ['Florida','Georgia','Alabama'],
                'clearance'   => ['type' => 'FDA 510(k)', 'number' => 'K221998', 'issue_date' => '2022-07-01'],
                'codes'       => [
                    ['code_type' => 'CPT','code' => '94010','description' => 'Spirometry, including graphic record'],
                ],
                'documents'   => [
                    ['type' => 'brochure','path'=>'devices/pulmoair/brochure.pdf'],
                ],
                'owner' => 'active',
            ],
            [
                'name'        => 'OrthoMotion Tracker',
                'model'       => 'OMT-2',
                'category'    => 'Orthopedics',
                'description' => 'Post-op mobility sensor with analytics for joint replacement recovery.',
                'indications' => 'Orthopedic post-surgical rehab and monitoring.',
                'fda_pathway' => 'exempt',
                'territories' => ['Texas','Oklahoma','Arkansas'],
                'clearance'   => ['type' => 'Class I Exempt'],
                'documents'   => [
                    ['type' => 'training','path'=>'devices/ortho-motion/training.pdf'],
                ],
                'owner' => 'inactive',
            ],
            [
                'name'        => 'NeuroPulse EEG Patch',
                'model'       => 'NP-8',
                'category'    => 'Neurology',
                'description' => 'Disposable EEG patch for ambulatory monitoring.',
                'indications' => 'Ambulatory EEG acquisition and analysis.',
                'fda_pathway' => '510k',
                'territories' => ['California','Nevada','Arizona'],
                'clearance'   => ['type' => 'De Novo', 'number' => 'DEN220045', 'issue_date' => '2022-11-20'],
                'codes'       => [
                    ['code_type' => 'CPT','code' => '95810','description'=>'Polysomnography; sleep staging with EEG'],
                ],
                'documents'   => [
                    ['type'=>'evidence','path'=>'devices/neuro-pulse/publications.pdf'],
                ],
                'owner' => 'inactive',
            ],
        ];

        foreach ($devices as $def) {
            $owner = ($def['owner'] ?? 'active') === 'active' ? $mActive : $mInactive;
            if ($owner) $makeDevice($def, $owner);
        }
    }
}

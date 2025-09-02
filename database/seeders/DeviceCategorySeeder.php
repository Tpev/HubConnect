<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class DeviceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            // Diagnostic Imaging
            'Diagnostic Imaging',
            'X-Ray (Digital Radiography)',
            'Mobile X-Ray',
            'Fluoroscopy / C-Arm',
            'Mammography',
            'CT Scanner',
            'MRI Scanner',
            'Ultrasound',
            'Bone Densitometry (DEXA)',
            'Nuclear Medicine / SPECT',
            'PET/CT',
            'Imaging Workstations & PACS Hardware',

            // Patient Monitoring
            'Patient Monitoring',
            'Multi-parameter Monitors',
            'Central Monitoring Stations',
            'Telemetry Systems',
            'ECG / EKG Machines',
            'Holter Monitors',
            'Stress Test Systems',
            'Ambulatory Blood Pressure Monitors (ABPM)',
            'Blood Pressure Monitors (Clinical)',
            'Pulse Oximeters (Clinical)',
            'Capnography Monitors',
            'Temperature Monitoring',
            'Fetal & Maternal Monitors',
            'Cardiac Output Monitors',
            'Remote Patient Monitoring (RPM) Kits',

            // Respiratory Care
            'Respiratory Care',
            'Ventilators (Invasive/Non-invasive)',
            'Transport Ventilators',
            'Oxygen Concentrators',
            'CPAP / BiPAP',
            'High-Flow Nasal Cannula Systems',
            'Nebulizers',
            'Spirometers',
            'Airway Clearance Devices',

            // Anesthesia
            'Anesthesia',
            'Anesthesia Machines',
            'Vaporizers (Anesthesia)',
            'Gas Scavenging Systems',

            // Operating Room / Surgery
            'Operating Room / Surgery',
            'OR Tables',
            'Surgical Lights',
            'Electrosurgical Units (ESU)',
            'Surgical Microscopes',
            'Insufflators (Laparoscopic)',
            'Endoscopy Towers',
            'Endoscopic Cameras',
            'Rigid Endoscopes',
            'Flexible Endoscopes',
            'Smoke Evacuators',
            'Patient Warmers (Forced-Air)',
            'Suction & Aspiration',
            'Tourniquet Systems',

            // Sterilization & Reprocessing
            'Sterilization & Reprocessing',
            'Steam Sterilizers / Autoclaves',
            'Low-Temperature Sterilizers',
            'Ultrasonic Cleaners',
            'Washer-Disinfectors',
            'Automated Endoscope Reprocessors (AER)',
            'UV Disinfection Systems',
            'Sterilization Monitoring (BI/CI)',

            // Infusion & Drug Delivery
            'Infusion & Drug Delivery',
            'Infusion Pumps (Volumetric)',
            'Syringe Pumps',
            'PCA Pumps',
            'Enteral Feeding Pumps',

            // Dialysis & Renal
            'Dialysis & Renal',
            'Hemodialysis Machines',
            'Peritoneal Dialysis Cyclers',
            'Dialysis Water Treatment',

            // Laboratory & Diagnostics
            'Laboratory & Diagnostics',
            'Hematology Analyzers',
            'Chemistry Analyzers',
            'Immunoassay Analyzers',
            'Coagulation Analyzers',
            'Blood Gas Analyzers',
            'Microbiology Analyzers',
            'PCR / Molecular Diagnostic Systems',
            'Point-of-Care Testing (POCT)',
            'Urinalysis Analyzers',
            'Clinical Refrigerators & Freezers',
            'Centrifuges (Clinical)',
            'Laboratory Incubators',

            // Oncology
            'Oncology',
            'Linear Accelerators (LINAC)',
            'Brachytherapy Equipment',
            'Infusion Chairs (Oncology)',

            // Women’s Health
            'Women’s Health',
            'Colposcopes',
            'OB / Fetal Dopplers',
            'OB Ultrasound',

            // Neonatal & Pediatrics
            'Neonatal & Pediatrics',
            'Infant Incubators',
            'Infant Warmers',
            'Phototherapy Units',
            'Neonatal Ventilators',

            // Neurology
            'Neurology',
            'EEG Systems',
            'EMG / NCS Systems',

            // Rehabilitation & Physical Therapy
            'Rehabilitation & Physical Therapy',
            'TENS / EMS Therapy',
            'Therapeutic Ultrasound (Physio)',
            'CPM Machines',
            'Traction Tables',

            // Orthopedics
            'Orthopedics',
            'Cast Saws',
            'Orthopedic Drills & Saws',

            // Dental
            'Dental',
            'Dental Chairs & Units',
            'Dental X-Ray & Sensors',
            'Dental Autoclaves',
            'Intraoral Scanners',

            // Ophthalmology
            'Ophthalmology',
            'Slit Lamps',
            'Autorefractors / Keratometers',
            'Phacoemulsification Systems',
            'Optical Coherence Tomography (OCT)',
            'Tonometers',
            'Visual Field Analyzers',

            // ENT / Audiology
            'ENT / Audiology',
            'ENT Microscopes',
            'Audiometers',
            'Tympanometers',
            'Video Otoscopes',

            // Urology
            'Urology',
            'Urodynamics Systems',
            'Lithotripters',
            'Cystoscopes',

            // Gastroenterology
            'Gastroenterology',
            'Gastroscopes',
            'Colonoscopes',
            'Capsule Endoscopy Systems',
            'Insufflators (GI)',

            // Vascular
            'Vascular',
            'Vascular Dopplers',
            'ABI / PVR Testing Systems',

            // Emergency & Critical Care
            'Emergency & Critical Care',
            'Defibrillators',
            'Portable Suction Units',
            'Emergency Ultrasound (POCUS)',

            // Hospital Infrastructure & Furniture
            'Hospital Infrastructure & Furniture',
            'Hospital Beds',
            'Birthing Beds',
            'Stretchers & Gurneys',
            'Exam Tables',
            'IV Poles',
            'Patient Lifts & Transfer',
            'Pressure-Relief Mattresses',

            // Wound Care
            'Wound Care',
            'Negative Pressure Wound Therapy (NPWT)',
            'Hyperbaric Oxygen Chambers',

            // Infection Prevention & Control
            'Infection Prevention & Control',
            'Air Purification (HEPA/UV)',
            'Hand Hygiene Monitoring',

            // IT / Telehealth
            'IT / Telehealth',
            'Telemedicine Carts',
            'Vital Signs Kiosks',
            'RPM Hubs & Gateways',

            // Home Health / DME
            'Home Health / DME',
            'Wheelchairs & Mobility',
            'Walkers & Canes',
            'Compression Therapy Pumps',
            'Home Sleep Apnea Testing (HSAT)',
            'Digital Scales (Medical)',
            'Blood Pressure Monitors (Home)',
            'Pulse Oximeters (Home)',
            'Thermometers (Medical)',
            'Glucose Monitors (Home)',

            // Miscellaneous
            'Specimen Transport & Storage',
        ];

        foreach ($names as $n) {
            Category::firstOrCreate(['name' => $n]);
        }
    }
}

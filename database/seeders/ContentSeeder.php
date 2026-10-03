<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\Facility;
use App\Models\ImpactMetric;
use App\Models\MediaAsset;
use App\Models\Program;
use App\Models\Region;
use App\Models\Story;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Programs
        $programs = [
            ['key' => 'water', 'title_ar' => 'المياه', 'title_en' => 'Water', 'description_ar' => 'سقيا، تحلية، وآبار.', 'description_en' => 'Water supply, desalination, and wells.', 'order' => 1],
            ['key' => 'food', 'title_ar' => 'الأمن الغذائي', 'title_en' => 'Food Security', 'description_ar' => 'طرود، إطعام، ومخبز بادر.', 'description_en' => 'Food parcels, relief meals, and bakery.', 'order' => 2],
            ['key' => 'shelter', 'title_ar' => 'الإيواء', 'title_en' => 'Shelter', 'description_ar' => 'احتياجات أساسية وترميم المنازل.', 'description_en' => 'Essential relief supplies and shelter rehabilitation.', 'order' => 3],
            ['key' => 'winter', 'title_ar' => 'الشتاء', 'title_en' => 'Winter Relief', 'description_ar' => 'دفء الشتاء للأسر النازحة.', 'description_en' => 'Winter warmth and aid for displaced families.', 'order' => 4],
            ['key' => 'health', 'title_ar' => 'الصحة', 'title_en' => 'Health', 'description_ar' => 'أدوية وعلاج للمرضى والجرحى.', 'description_en' => 'Medicines and medical support for patients and wounded.', 'order' => 5],
            ['key' => 'education', 'title_ar' => 'التعليم', 'title_en' => 'Education', 'description_ar' => 'مدرسة بادر واستمرار التعلم.', 'description_en' => 'Bader school and continuous education.', 'order' => 6],
            ['key' => 'social_protection', 'title_ar' => 'الحماية الاجتماعية', 'title_en' => 'Social Protection', 'description_ar' => 'كفالة الأرامل والأيتام والدعم النفسي والاجتماعي.', 'description_en' => 'Orphan and widow sponsorship with psychosocial care.', 'order' => 7],
            ['key' => 'economic_empowerment', 'title_ar' => 'التمكين الاقتصادي', 'title_en' => 'Economic Empowerment', 'description_ar' => 'تدريب وبرامج تنموية ومساندة مشاريع صغيرة للأسر.', 'description_en' => 'Vocational training, micro-grants, and livelihood support.', 'order' => 8],
            ['key' => 'community_partnerships', 'title_ar' => 'الشراكات والمبادرات المجتمعية', 'title_en' => 'Community Partnerships', 'description_ar' => 'مبادرات تعزز التكافل والتعاون مع الجهات المحلية والدولية.', 'description_en' => 'Collaborative initiatives with local and international partners.', 'order' => 9],
            ['key' => 'zakat', 'title_ar' => 'الزكاة', 'title_en' => 'Zakat', 'description_ar' => 'إيصال الزكاة إلى مستحقيها عبر قنوات موثوقة.', 'description_en' => 'Distributing verified Zakat to eligible families.', 'order' => 10],
            ['key' => 'sacrifices', 'title_ar' => 'الأضاحي', 'title_en' => 'Sacrifices & Qurbani', 'description_ar' => 'ذبح وتوزيع الأضاحي والهدي والعقيقة والفدية والصدقة.', 'description_en' => 'Qurbani and meat distribution for families in need.', 'order' => 11],
        ];

        foreach ($programs as $prog) {
            Program::updateOrCreate(['key' => $prog['key']], $prog + ['status' => 'published']);
        }

        // 2. Work regions inside the Gaza Strip (map_x / map_y are percentages on the region map)
        $regions = [
            ['key' => 'north_gaza', 'name_ar' => 'شمال غزة', 'name_en' => 'North Gaza', 'description_ar' => 'استجابة إغاثية للأسر العائدة والنازحة في محافظة الشمال.', 'description_en' => 'Relief response for returning and displaced families in the northern governorate.', 'map_x' => 82, 'map_y' => 16, 'map_area' => 'north_gaza', 'order' => 1],
            ['key' => 'gaza_city', 'name_ar' => 'مدينة غزة', 'name_en' => 'Gaza City', 'description_ar' => 'مياه وغذاء وتعليم في أكبر تجمع سكاني في القطاع.', 'description_en' => 'Water, food and education in the strip\'s largest population centre.', 'map_x' => 64, 'map_y' => 30, 'map_area' => 'gaza_city', 'order' => 2],
            ['key' => 'middle_area', 'name_ar' => 'الوسطى ودير البلح', 'name_en' => 'Middle Area & Deir al-Balah', 'description_ar' => 'مقر العمليات الميدانية ومرافق بادر التشغيلية.', 'description_en' => 'Home of Bader\'s field operations and operating facilities.', 'map_x' => 44, 'map_y' => 47, 'map_area' => 'middle_area', 'order' => 3],
            ['key' => 'khan_younis', 'name_ar' => 'خان يونس', 'name_en' => 'Khan Younis', 'description_ar' => 'إيواء ودعم صحي للأسر في مخيمات النزوح.', 'description_en' => 'Shelter and health support for families in displacement camps.', 'map_x' => 30, 'map_y' => 68, 'map_area' => 'khan_younis', 'order' => 4],
            ['key' => 'rafah', 'name_ar' => 'رفح', 'name_en' => 'Rafah', 'description_ar' => 'تدخلات طارئة وفق إمكانية الوصول الميداني.', 'description_en' => 'Emergency interventions as field access allows.', 'map_x' => 17, 'map_y' => 82, 'map_area' => 'rafah', 'order' => 5],
        ];

        foreach ($regions as $region) {
            Region::updateOrCreate(['key' => $region['key']], $region + ['status' => 'published']);
        }

        $regionIds = Region::pluck('id', 'key');
        $programIds = Program::pluck('id', 'key');

        // 3. Facilities
        $facilities = [
            ['key' => 'water_plant', 'region_id' => $regionIds['middle_area'], 'name_ar' => 'محطة بادر للتحلية', 'name_en' => 'Bader Desalination Plant', 'description_ar' => 'مياه شرب آمنة كأصل تشغيلي مستمر.', 'description_en' => 'Safe drinking water as an ongoing operational asset.', 'location_ar' => 'قطاع غزة', 'location_en' => 'Gaza Strip', 'order' => 1],
            ['key' => 'bakery', 'region_id' => $regionIds['middle_area'], 'name_ar' => 'مخبز بادر البلدي', 'name_en' => 'Bader Community Bakery', 'description_ar' => 'خبز يومي يصل إلى الأسر لا عبر شعار فقط.', 'description_en' => 'Fresh daily bread reaching families on the ground.', 'location_ar' => 'قطاع غزة', 'location_en' => 'Gaza Strip', 'order' => 2],
            ['key' => 'school', 'region_id' => $regionIds['gaza_city'], 'name_ar' => 'مدرسة بادر التعليمية', 'name_en' => 'Bader Educational School', 'description_ar' => 'مقاعد دراسية بعد أن دُمّرت المدارس.', 'description_en' => 'Classrooms and learning spaces amid destroyed schools.', 'location_ar' => 'قطاع غزة', 'location_en' => 'Gaza Strip', 'order' => 3],
        ];

        foreach ($facilities as $fac) {
            Facility::updateOrCreate(['key' => $fac['key']], $fac + ['status' => 'published']);
        }

        // 4. Campaigns
        $campaigns = [
            [
                'key' => 'water',
                'program_id' => $programIds['water'],
                'region_id' => $regionIds['middle_area'],
                'image' => 'images/programs/water.jpg',
                'preset_amounts' => [10, 25, 50],
                'title_ar' => 'مشروع سقيا الماء',
                'title_en' => 'Water Supply Project',
                'description_ar' => 'توفير مياه شرب آمنة ونقية للنازحين والأسر المتضررة في قطاع غزة عبر مشروع مستدام.',
                'description_en' => 'Providing clean drinking water to displaced and affected families in Gaza through a sustainable initiative.',
                'goal_amount' => 120000,
                'raised_amount' => 0,
                'currency_ar' => 'دولار أمريكي',
                'currency_en' => 'USD',
                'is_featured' => true,
                'status' => 'published',
            ],
            [
                'key' => 'education',
                'program_id' => $programIds['education'],
                'region_id' => $regionIds['gaza_city'],
                'image' => 'images/programs/education.jpg',
                'preset_amounts' => [5, 10, 25],
                'title_ar' => 'أكاديمية بادر لتعليم وتحفيظ القرآن الكريم',
                'title_en' => 'Bader Quran & Education Academy',
                'description_ar' => 'دعم التعليم وتحفيظ القرآن الكريم للطلبة وبناء مساحة أمل واستمرار في التعلم.',
                'description_en' => 'Supporting continuous learning and Quran education for students, creating spaces of hope.',
                'goal_amount' => null,
                'raised_amount' => 0,
                'currency_ar' => 'دولار أمريكي',
                'currency_en' => 'USD',
                'is_featured' => true,
                'status' => 'published',
            ],
        ];

        foreach ($campaigns as $camp) {
            Campaign::updateOrCreate(['key' => $camp['key']], $camp);
        }

        // 5. Stories
        $stories = [
            [
                'key' => 'quran_honor',
                'title_ar' => 'تكريم حفظة القرآن',
                'title_en' => 'Honoring Quran Memorizers',
                'excerpt_ar' => 'مؤسسة بادر تكرّم المئات من حفظة كتاب الله من طلبة مدرسة بادر التعليمية.',
                'excerpt_en' => 'Bader Foundation honors hundreds of students for Quran memorization.',
                'category_ar' => 'تعليم',
                'category_en' => 'Education',
                'published_at' => '2025-04-29',
                'is_featured' => true,
                'status' => 'published',
            ],
            [
                'key' => 'deir_balah',
                'title_ar' => 'من قلب دير البلح',
                'title_en' => 'From the Heart of Deir al-Balah',
                'excerpt_ar' => 'مؤسسة بادر الإنسانية تزور بلدية دير البلح لتقديم التهنئة للمجلس المنتخب.',
                'excerpt_en' => 'Bader Humanitarian visits Deir al-Balah municipality to congratulate the elected council.',
                'category_ar' => 'ميدان',
                'category_en' => 'Field',
                'published_at' => '2025-04-29',
                'is_featured' => false,
                'status' => 'published',
            ],
            [
                'key' => 'quran_camp',
                'title_ar' => 'أكاديمية بادر لتحفيظ وتعليم القرآن الكريم',
                'title_en' => 'Bader Quran Camp & Academy',
                'excerpt_ar' => 'التعليم المستمر وتحفيظ القرآن الكريم مساحة للعلم والأمل وبناء المستقبل.',
                'excerpt_en' => 'Continuous education and Quran study as a sanctuary for hope and youth development.',
                'category_ar' => 'مجتمع',
                'category_en' => 'Community',
                'published_at' => '2025-04-29',
                'is_featured' => false,
                'status' => 'published',
            ],
        ];

        foreach ($stories as $st) {
            Story::updateOrCreate(['key' => $st['key']], $st);
        }

        // 5. Media Assets with consent
        MediaAsset::updateOrCreate(
            ['file_path' => 'brand/logo-dark1.jpg'],
            [
                'title_ar' => 'شعار مؤسسة بادر الرسمي',
                'title_en' => 'Official Bader Foundation Logo',
                'category' => 'brand',
                'has_usage_consent' => true,
                'consent_notes' => 'المواد البصرية الرسمية المعتمدة للمؤسسة.',
            ]
        );

        // 6. Impact Metrics
        $metrics = [
            [
                'key' => 'families',
                'title_ar' => 'إجمالي الأسر المستفيدة',
                'title_en' => 'Beneficiary Families',
                'value' => '45,000+',
                'unit_ar' => 'أسرة',
                'unit_en' => 'Families',
                'category' => 'general',
                'order' => 1,
                'is_approved' => true,
                'status' => 'approved',
                'approved_at' => now(),
            ],
            [
                'key' => 'water',
                'title_ar' => 'مياه شرب نقية موزعة',
                'title_en' => 'Clean Drinking Water',
                'value' => '1.2M+',
                'unit_ar' => 'لتر',
                'unit_en' => 'Liters',
                'category' => 'water',
                'order' => 2,
                'is_approved' => true,
                'status' => 'approved',
                'approved_at' => now(),
            ],
            [
                'key' => 'bread',
                'title_ar' => 'أرغفة خبز يومية',
                'title_en' => 'Daily Bread Loaves',
                'value' => '350,000+',
                'unit_ar' => 'رغيف',
                'unit_en' => 'Loaves',
                'category' => 'food',
                'order' => 3,
                'is_approved' => true,
                'status' => 'approved',
                'approved_at' => now(),
            ],
            [
                'key' => 'students',
                'title_ar' => 'طلاب مستمرون في التعليم',
                'title_en' => 'Students Supported',
                'value' => '3,200+',
                'unit_ar' => 'طالب',
                'unit_en' => 'Students',
                'category' => 'education',
                'order' => 4,
                'is_approved' => true,
                'status' => 'approved',
                'approved_at' => now(),
            ],
        ];

        foreach ($metrics as $metric) {
            ImpactMetric::updateOrCreate(['key' => $metric['key']], $metric);
        }
    }
}
